<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 06.06.18
 * Time: 16:26
 */

namespace App\ManufacturerApi;


use App\Lib\IManufacturerAPI;
use App\Models\Meter\MeterToken;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use SoapClient;
use function config;

class CalinApi implements IManufacturerAPI
{

    /**
     * @var Client
     */
    protected $api;

    public function __construct(Client $httpClient)
    {
        $this->api = $httpClient;
    }


    function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json;charset=utf-8',
                'Content-Length:' . strlen($jsonStr),
            ]
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$httpCode, $response];
    }

    /**
     * @param $tokenData
     *
     * @return MeterToken
     * @throws Exception
     */
    public function generateToken($tokenData): MeterToken
    {
        $timestamp = time();
        $toCharge = round($tokenData->transaction->amount / (float)($tokenData->tariff->price / 100), 1);
        $cipherText = $this->generateCipherText($tokenData->meter->id, 'INENSUS',
            $tokenData->meter->serial_number, 'CreditToken', $toCharge, $timestamp);

        $tokenParams = [
            'serial_id' => $tokenData->meter->id,
            'user_id' => 'INENSUS',
            'meter_id' => $tokenData->meter->serial_number,
            'token_type' => 'CreditToken',
            'amount' => $toCharge,
            'timestamp' => $timestamp,
            'ciphertext' => $cipherText,
        ];
        //TODO make request in a seperate funciton
        $request = $this->api->post(
            "http://api.calinhost.com/api/token",
            [
                'body' => json_encode($tokenParams),
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'Content-Length:' . strlen(json_encode($tokenParams)),
                ],
            ]
        );


        $tokenResult = json_decode((string)$request->getBody(), true);
        //token generation failed, re-try to re-create the token 2 more times
        if ((int)$tokenResult['result_code'] !== 0) {
            Log::critical('Token generation failed', $tokenParams);
            throw  new Exception($tokenResult['reason']);

        }
        $token = $tokenResult['result'];


        $tokenModel = new MeterToken();
        $tokenModel->token = $token;
        $tokenModel->energy = $toCharge;

        return $tokenModel;
    }

    private function initClient(): void
    {
        $this->apiClient = new SoapClient(config()->get('services.calin.api'), ['keep_alive' => false]);
    }

    private function generateCipherText($serialID, $userID, $meterID, $tokenType, $amount, $timestamp): string
    {
        return md5(
            sprintf('%s%s%s%s%s%s%s',
                $serialID, $userID, $meterID, $tokenType, $amount, $timestamp, config('services.calin.key'))
        );
    }
}
