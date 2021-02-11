<?php

namespace App\Listeners;

use App\Misc\TransactionDataContainer;
use App\Models\Sms;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use function config;

class SmsListener
{

    /**
     * @param string                   $number
     * @param int                      $type
     * @param TransactionDataContainer $data
     * @param $trigger
     *
     * @return bool
     */
    public function sendSms($number, $data, $trigger): bool
    {
        if (config('app.debug')) {
            //store sent sms
            $sms = new Sms();
            $sms->body = SmsTypes::generateSmsBody($data);
            $sms->receiver = $number;
            $sms->trigger()->associate($trigger);
            $sms->save();
            Log::debug(
                'Debug Sms Sent',
                ['number' => $number, 'body' => $sms->body, 'id' => 'ht47ehrfdjkgh378hfdjkldkddms']
            );
            return true;
        }
        try {
            //sends sms or throws exception
            resolve('SmsProvider')->sendSms($number, SmsTypes::generateSmsBody($data), '');
        } catch (Exception $e) {
            //slack failure
            Log::critical(
                'Sms Service failed ' . $number,
                ['id' => '58365682988725', 'reason' => $e->getMessage()]
            );
            return false;
        }
        //store sent sms
        $sms = new Sms();
        $sms->body = SmsTypes::generateSmsBody($data);
        $sms->receiver = $number;
        $sms->trigger()->associate($trigger);
        $sms->save();
        return true;
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen('sms.send.token', '\App\Listeners\SmsListener@sendSms');
        $events->listen('sms.send', '\App\Listeners\SmsListener@sendSms');
    }
}
