<?php

namespace App\Jobs;

use App\Lib\IManufacturerAPI;
use App\Misc\TransactionDataContainer;
use App\Models\Meter\MeterToken;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use function config;


class TokenProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var TransactionDataContainer
     */
    private $transactionContainer;

    /**
     * @var bool
     */

    private $reCreate;
    /**
     * @var int
     */
    private $counter;

    private const maxTries = 3;
    /**
     * @var IManufacturerAPI
     */
    private $api;

    /**
     * Create a new job instance.
     *
     * @param IManufacturerAPI $api
     * @param TransactionDataContainer $container
     * @param bool $reCreate is a flag which determines to create a new token or not
     * @param int $counter
     */
    public function __construct(
        IManufacturerAPI $api,
        TransactionDataContainer $container,
        bool $reCreate = false,
        int $counter = self::maxTries
    ) {
        $this->transactionContainer = $container;
        $this->reCreate = $reCreate;
        $this->counter = $counter;
        $this->api = $api;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function handle(): void
    {

        $token = $this->transactionContainer->transaction->token()->first();
        if ($token !== null & $this->reCreate === true) {
            $token->delete();
            $token = null;
        }
        //no token generated before
        if ($token === null) {
            try {
                $tokenData = $this->api->generateToken(
                    $this->transactionContainer->meter,
                    $this->transactionContainer->chargedEnergy
                );
            } catch (Exception $e) {
                if (self::maxTries > $this->counter) {
                    $this->counter++;
                    //re-queue the job in five seconds
                    self::dispatch($this->api, $this->transactionContainer, false,
                        $this->counter)->allOnConnection('redis')->onQueue(config('services.queues.token'))->delay(5);
                    return;
                }
                Log::critical($this->transactionContainer->manufacturer->name . ' Token listener failed after  ' . $this->counter . 'times ',
                    ['id' => '4627573927', 'message' => $e->getMessage()]);
                event('transaction.failed', [
                    $this->transactionContainer->transaction,
                    'Token generation request failed with following reason ' . $e->getMessage()
                ],);
                return;
            }
            $token = app()->make(MeterToken::class);
            $token->token = $tokenData['token'];
            $token->energy = $tokenData['energy'];
            $token->transaction()->associate($this->transactionContainer->transaction);
            $token->meter()->associate($this->transactionContainer->meter);
            //save token
            $token->save();
        }
        //add token to tokenData
        $this->transactionContainer->token = $token;

        // payment event
        event('payment.successful', [
            'amount' => $this->transactionContainer->transaction->amount,
            'paymentService' => $this->transactionContainer->transaction->original_transaction_type,
            'paymentType' => 'energy',
            'sender' => $this->transactionContainer->transaction->sender,
            'paidFor' => $token,
            'payer' => $this->transactionContainer->meterParameter->owner,
            'transaction' => $this->transactionContainer->transaction,
        ]);

        event('transaction.successful', [$this->transactionContainer->transaction]);


        SmsProcessor::dispatch($this->transactionContainer,
            SmsTypes::ENERGY_CONFIRMATION)->allOnConnection('redis')->onQueue('sms');
    }

}
