<?php

namespace App\Jobs;

use App\Exceptions\SmsBodyParserNotExtendedException;
use App\Exceptions\SmsTypeNotFoundException;
use App\Models\Sms;
use App\Sms\Senders\ManualSms;
use App\Sms\Senders\SmsSender;
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
    private $smsConfigs;

    /**
     * Create a new job instance.
     *
     * @param     $data
     * @param int $smsType
     * @param $smsConfigs
     */
    public function __construct($data, int $smsType, $smsConfigs)
    {
        $this->data = $data;
        $this->smsType = $smsType;
        $this->smsConfigs = $smsConfigs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $smsType = $this->resolveSmsType();
        } catch (SmsTypeNotFoundException $exception) {
            Log::critical('Sms send failed.', ['message : ' => $exception->getMessage()]);
            return;
        } catch (SmsBodyParserNotExtendedException $exception) {
            Log::critical('Sms send failed.', ['message : ' => $exception->getMessage()]);
            return;
        }
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
            Log::critical(
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
        $configs = resolve($this->smsConfigs);
        if (!array_key_exists($this->smsType, $configs->smsTypes)) {
            throw new  SmsTypeNotFoundException('SmsType could not resolve.');
        }
        $smsBodyService = resolve($configs->servicePath);
        $reflection = new \ReflectionClass($configs->smsTypes[$this->smsType]);
        if (!$reflection->isSubclassOf(SmsSender::class)) {
            throw new  SmsBodyParserNotExtendedException('SmsBodyParser has not extended.');
        }
        return $reflection->newInstanceArgs([$this->data,$smsBodyService,$configs->bodyParsersPath]);
    }
}
