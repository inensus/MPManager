<?php

namespace App\Jobs;

use App\Misc\TransactionDataContainer;
use App\Models\Sms;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;
use function config;


class SmsProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $transactionContainer;
    private $smsType;

    /**
     * Create a new job instance.
     *
     * @param TransactionDataContainer $container
     * @param int $smsType
     */
    public function __construct(TransactionDataContainer $container, int $smsType)
    {
        $this->transactionContainer = $container;
        $this->smsType = $smsType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sms = new Sms();
        //dont send sms if debug
        if (config('app.debug')) {
            //store sent sms

            $sms->body = SmsTypes::generateSmsBody($this->smsType, $this->transactionContainer);
            $sms->receiver = $this->transactionContainer->transaction->sender;
            $sms->trigger()->associate($this->transactionContainer->transaction);
            $sms->save();
            return;
        }


        try {
            //set the uuid for the callback
            $sms->uuid = (string)Uuid::generate(4);
            //sends sms or throws exception
            resolve('SmsProvider')
                ->sendSms(
                    $this->transactionContainer->transaction->sender,
                    SmsTypes::generateSmsBody($this->smsType, $this->transactionContainer),
                    sprintf(config()->get('services.sms.callback'), $sms->uuid)
                );
        } catch (Exception $e) {
            //slack failure
            Log::critical('Sms Service failed ' . $this->transactionContainer->transaction->sender,
                ['id' => '58365682988725', 'reason' => $e->getMessage()]);
            return;
        }
        $phone = $this->transactionContainer->transaction->sender;
        $sms->body = SmsTypes::generateSmsBody($this->smsType, $this->transactionContainer);
        $sms->receiver = strpos($phone, '+') === 0 ? $phone : '+' . $phone;
        $sms->trigger()->associate($this->transactionContainer->transaction);
        $sms->save();
    }
}
