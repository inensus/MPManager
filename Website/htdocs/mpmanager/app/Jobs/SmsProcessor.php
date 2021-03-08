<?php

namespace App\Jobs;

use App\Exceptions\SmsBodyParserNotExtendedException;
use App\Exceptions\SmsTypeNotFoundException;
use App\Models\Sms;
use App\Sms\BodyParsers\SmsBodyParser;
use App\Sms\Senders\ManualSms;
use App\Sms\Senders\SmsSender;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SmsProcessor implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public $data;
    public $smsType;
    private $smsTypes = [
        SmsTypes::TRANSACTION_CONFIRMATION => 'App\Sms\Senders\TransactionConfirmation',
        SmsTypes::APPLIANCE_RATE => 'App\Sms\Senders\AssetRateNotification',
        SmsTypes::OVER_DUE_APPLIANCE_RATE => 'App\Sms\Senders\OverDueAssetRateNotification',
        SmsTypes::MANUAL_SMS => 'App\Sms\Senders\ManualSms',
        SmsTypes::RESEND_INFORMATION => 'App\Sms\Senders\ResendInformationNotification'
    ];

    /**
     * Create a new job instance.
     *
     * @param            $data
     * @param SmsTypes $smsType
     */
    public function __construct($data, int $smsType)
    {
        $this->data = $data;
        $this->smsType = $smsType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $smsType = $this->resolveSmsType();
        $receiver = $smsType->getReceiver();
        //dont send sms if debug
        if (config('app.debug')) {
            if (!($smsType instanceof ManualSms)) {
                $sms = Sms::query()->make([
                    'body' => $smsType->body,
                    'receiver' => $receiver,
                    'uuid' => "debug"
                ]);
                $sms->trigger()->associate($this->data);
                $sms->save();
                return;
            }
        }
        try {
            //set the uuid for the callback
            $uuid = $smsType->generateCallback();
            //sends sms or throws exception
            $smsType->sendSms();
        } catch (Exception $e) {
            //slack failure
            Log::debug(
                'Sms Service failed ' . $receiver,
                ['id' => '58365682988725', 'reason' => $e->getMessage()]
            );
            return;
        }
        if (!($smsType instanceof ManualSms)) {
            $sms = Sms::query()->make([
                'uuid' => $uuid,
                'body' => $smsType->body,
                'receiver' => $receiver
            ]);
            $sms->trigger()->associate($this->data);
            $sms->save();
        }
    }

    private function resolveSmsType()
    {
        if (!array_key_exists($this->smsType, $this->smsTypes)) {
            throw new  SmsTypeNotFoundException('SmsType could not resolve.');
        }

        $smsBodyService = resolve('App\Services\SmsBodyService');
        $reflection = new \ReflectionClass($this->smsTypes[$this->smsType]);

        if (!$reflection->isSubclassOf(SmsSender::class)) {
            throw new  SmsBodyParserNotExtendedException('SmsBodyParser has not extended 5.');
        }
        return $reflection->newInstanceArgs([$this->data, $smsBodyService]);
    }
}
