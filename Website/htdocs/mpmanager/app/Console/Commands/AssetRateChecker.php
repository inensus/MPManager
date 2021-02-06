<?php

namespace App\Console\Commands;

use App\Models\AssetRate;
use App\Models\User;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Console\Command;
use Inensus\Ticket\Models\Label;
use Inensus\Ticket\Services\BoardService;
use Inensus\Ticket\Services\CardService;
use Inensus\Ticket\Services\TicketService;

class AssetRateChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset.rate:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if any asset rate is due and creates a ticket and reminds the customer';
    /**
     * @var AssetRate
     */
    private $assetRate;
    /**
     * @var BoardService
     */
    private $boardService;
    /**
     * @var CardService
     */
    private $cardService;
    /**
     * @var TicketService
     */
    private $ticketService;

    /**
     * Create a new command instance.
     *
     * @param AssetRate     $assetRate
     * @param BoardService  $boardService
     * @param CardService   $cardService
     * @param TicketService $ticketService
     */
    public function __construct(
        AssetRate $assetRate,
        BoardService $boardService,
        CardService $cardService,
        TicketService $ticketService
    ) {
        parent::__construct();
        $this->assetRate = $assetRate;
        $this->boardService = $boardService;
        $this->cardService = $cardService;
        $this->ticketService = $ticketService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->remindUpComingRates(3);
        $this->findOverDueRates();

        return 0;
    }

    /**
     * Finds the rates which are overdue and edits the previously created ticket
     * and asks the customer to pay the rate
     */
    private function findOverDueRates(): void
    {
        $date = date('Y-m-d');
        $over_due_rates = $this->assetRate::with(['assetPerson.assetType', 'assetPerson.person.addresses'])
            ->whereDate('due_date', '<', $date)
            ->where('remaining', '>', 0)
            ->where('remind', 0)
            ->get();

        echo "\n" . count($over_due_rates) . ' overdue rates found' . "\n";
        foreach ($over_due_rates as $over_due_rate) {
            //send sms reminder to customer
            $this->sendReminderSms($over_due_rate, SmsTypes::ASSET_RATE_OVER_DUE_REMINDER);

            //set the remind flag to follow up
            $over_due_rate->remind = 1;
            $over_due_rate->update();

            $this->createReminderTicket($over_due_rate);
        }
    }


    /**
     * Finds rates which are almost due and reminds the customer
     * and creates also a ticket for the customer support
     *
     * @param int $difference
     */
    private function remindUpComingRates(int $difference): void
    {
        $base_time = time();
        $rate_date = date('Y-m-t', strtotime('+' . $difference . ' days', $base_time));
        $due_asset_rates = $this->assetRate::with(['assetPerson.assetType', 'assetPerson.person.addresses'])
            ->whereDate('due_date', '<=', $rate_date)
            ->where('remaining', '>', 0)
            ->whereHas(
                'assetPerson.person.addresses',
                static function ($q) {
                    $q->where('is_primary', 1);
                }
            )
            ->get();


        echo "\n" . count($due_asset_rates) . ' upcoming rates found' . "\n";

        foreach ($due_asset_rates as $asset_rate) {
            //send sms reminder to customer
            $this->sendReminderSms($asset_rate, SmsTypes::ASSET_RATE_REMINDER);


            $this->createReminderTicket($asset_rate);
        }
    }

    private function sendReminderSms(AssetRate $assetRate, int $smsType): void
    {
        event(
            'sms.send',
            [
                'sender' => $assetRate->assetPerson->person->addresses[0]->phone,
                'data' => $assetRate,
                'trigger' => $assetRate,
            ]
        );
    }


    private function createReminderTicket(AssetRate $assetRate, $overDue = false): void
    {
        //create ticket for customer service
        $board = $this->boardService->initializeBoard();
        $card = $this->cardService->initalizeList($board);
        $creator = User::where('name', 'System')->first();
        //reformat due date if it is set
        if ($overDue) {
            $category = Label::where('label_name', 'Payments Issue')->first();
            $description = 'Customer didn\'t pay ' . $assetRate->remaining . 'TZS on ' . $assetRate->due_date;
        } else {
            $category = Label::where('label_name', 'Customer Follow Up')->first();
            $description = 'Customer should pay ' . $assetRate->remaining . 'TZS until ' . $assetRate->due_date;
        }

        $trelloParams = [
            'idList' => $card->card_id,
            'name' => $assetRate->assetPerson->assetType->name . ' rate reminder',
            'desc' => $description,
            'due' => $assetRate->due_date === '1970-01-01' ? null : $assetRate->due_date,
            'idMembers' => null,

        ];


        try {
            $this->ticketService->create(
                $creator->id,
                $assetRate->assetPerson->person->id,
                'person',
                $category->id,
                null,
                $trelloParams
            );
        } catch (Exception $exception) {
            echo 'Ticket Creation failed with following reason :' . $exception->getMessage();
        }
    }
}
