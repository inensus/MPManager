<?php


namespace App\Services;


use GuzzleHttp\Client;

class FirebaseService
{


    public function sendNotify($firebaseToken,$body)
    {
        $httpClient = new Client();
        $request = $httpClient->post(config()->get('services.sms.android.url'),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => config()->get('services.agent.token'),
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'data' => $body,
                    'to' => $firebaseToken,
                ],
            ]
        );
        return (string)$request->getBody();
     }
}
