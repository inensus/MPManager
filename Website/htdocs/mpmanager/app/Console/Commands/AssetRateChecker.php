<?php

namespace App\Console\Commands;

use App\Jobs\SmsProcessor;
use App\Models\AssetRate;
use App\Models\User;
use App\Services\SmsApplianceRemindRateService;
use App\Sms\SmsTypes;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Inensus\Ticket\Models\Label;
use Inensus\Ticket\Services\BoardService;
use Inensus\Ticket\Services\CardService;
use Inensus\Ticket\Services\TicketService;

class AssetRateChecker extends Command
{

    protected $signature = 'asset-rate:check';


    protected $description = 'Checks if any asset rate is due and creates a ticket and reminds the customer';

    private $assetRate;
    private $user;
    private $boardService;
    private $cardService;
    private $ticketService;
    private $smsApplianceRemindRateService;
    private $label;


    public function __construct(
        AssetRate $assetRate,
        BoardService $boardService,
        CardService $cardService,
        TicketService $ticketService,
        SmsApplianceRemindRateService $smsApplianceRemindRateService,
        User $user,
        Label $label
    ) {
        parent::__construct();
        $this->assetRate = $assetRate;
        $this->boardService = $boardService;
        $this->cardService = $cardService;
        $this->ticketService = $ticketService;
        $this->smsApplianceRemindRateService = $smsApplianceRemindRateService;
        $this->user = $user;
        $this->label = $label;
    }


    public function handle()
    {
        $this->remindUpComingRates();
        $this->findOverDueRates();
    }


    private function findOverDueRates(): void
    {
        $smsApplianceRemindRates = $this->getApplianceRemindRates();
        $smsApplianceRemindRates->each(function ($smsApplianceRemindRate) {
            try {
                $overDueRates = $this->assetRate::with(['assetPerson.assetType', 'assetPerson.person.addresses'])
                    ->whereDate(
                        'due_date',
                        '>=',
                        Carbon::parse('due_date')->addDays($smsApplianceRemindRate->overdue_remind_rate)
                    )
                    ->where('remaining', '>', 0)
                    ->where('remind', 0)
                    ->get();
            } catch (ModelNotFoundException $exception) {
                Log::error('OverDue AssetRate not found', ['message' => $exception->getMessage()]);
            }

            echo "\n" . count($overDueRates) . ' overdue rates found' . "\n";
            $this->sendReminders($overDueRates, SmsTypes::OVER_DUE_APPLIANCE_RATE);
        });
    }

    private function remindUpComingRates(): void
    {
        $smsApplianceRemindRates = $this->getApplianceRemindRates();
        $smsApplianceRemindRates->each(function ($smsApplianceRemindRate) {
            $remindDay = Carbon::now()->subDays($smsApplianceRemindRate->remind_rate)->format('Y-m-d');
            try {
                $dueAssetRates = $this->assetRate::with([
                'assetPerson.assetType.smsReminderRate',
                'assetPerson.person.addresses'
                ])
                ->whereDate('due_date', '>=', $remindDay)
                ->where('remaining', '>', 0)
                ->whereHas(
                    'assetPerson.person.addresses',
                    static function ($q) {
                        $q->where('is_primary', 1);
                    }
                )
                ->get();
            } catch (ModelNotFoundException $exception) {
                Log::error('Due AssetRate not found', ['message' => $exception->getMessage()]);
            }
             echo "\n" . count($dueAssetRates) . ' upcoming rates found' . "\n";
             $this->sendReminders($dueAssetRates, SmsTypes::APPLIANCE_RATE);
        });
    }

    private function sendReminderSms(AssetRate $assetRate): void
    {
        SmsProcessor::dispatch(
            $assetRate,
            SmsTypes::APPLIANCE_RATE
        )->allOnConnection('redis')->onQueue(\config('services.queues.sms'));
    }

    private function sendReminders($dueAssetRates, $smsType)
    {
        $dueAssetRates->each(function ($dueAssetRate) use ($smsType) {
            $this->sendReminderSms($dueAssetRate);
            if ($smsType === SmsTypes::OVER_DUE_APPLIANCE_RATE) {
                $dueAssetRate->remind = 1;
                $dueAssetRate->update();
            }
            $this->createReminderTicket($dueAssetRate);
        });
    }

    private function createReminderTicket(AssetRate $assetRate, $overDue = false): void
    {
        //create ticket for customer service
        $board = $this->boardService->initializeBoard();
        $card = $this->cardService->initalizeList($board);
        $creator = $this->user->newQuery()->where('name', 'System')->first();
        //reformat due date if it is set
        if ($overDue) {
            $category = $this->label->newQuery()->where('label_name', 'Payments Issue')->first();
            $description = 'Customer didn\'t pay ' . $assetRate->remaining . 'TZS on ' . $assetRate->due_date;
        } else {
            $category = $this->label->newQuery()->where('label_name', 'Customer Follow Up')->first();
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

    private function getApplianceRemindRates()
    {
        return $this->smsApplianceRemindRateService->getApplianceRemindRates();
    }
}
