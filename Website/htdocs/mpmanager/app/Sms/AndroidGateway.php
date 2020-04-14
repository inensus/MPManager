<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 13.01.19
 * Time: 22:49
 */

namespace App\Sms;


use App\Jobs\SmsLoadBalancer;
use App\Lib\ISmsProvider;
use function config;
use function GuzzleHttp\Psr7\build_query;
use Illuminate\Support\Facades\Log;

class AndroidGateway implements ISmsProvider
{

    /**
     * Sends the sms to the sms provider
     * @param string $number
     * @param string $body
     * @param string $callback
     */
    public function sendSms(string $number, string $body, string $callback)
    {

        if (config('app.debug')) {
            Log::debug('Send sms on debug is not allowed', ['number' => $number, 'message' => $body]);
            return;
        }

        //add sms to sms_gateway job
        SmsLoadBalancer::dispatch([
            'number' => $number,
            'message' => $body,
            'sms_id' => $callback,
        ])->onConnection('redis')->onQueue('sms_gateway');
    }
}
