<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 21.01.19
 * Time: 12:26
 */

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SmsLoadBalancer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 600;
    public $tries = 250;
    public $gateways;


    public $smsBody;

    public function __construct($smsBody)
    {
        $this->smsBody = $smsBody;
    }

    public function handle(): void
    {
        Redis::throttle('smsgateway')->allow(1)->every(1)->block(1)->then(
            function () {
                $fireBaseResult = $this->sendSms($this->smsBody);
                Log::debug('smsgateway', ['data' => $this->smsBody, 'firebase' => $fireBaseResult]);
            },
            function () {
                return $this->release(1);
            }
        );
    }

    private function sendSms($data): string
    {
        $httpClient = new Client();
        $request = $httpClient->post(
            config()->get('services.sms.android.url'),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'key=' . config()->get('services.sms.android.key'),
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'data' => $data,
                    'to' => config()->get('services.sms.android.token'),
                ],
            ]
        );
        return (string)$request->getBody();
    }
}
