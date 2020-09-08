<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 06.06.18
 * Time: 16:26
 */

namespace App\ManufacturerApi;


use App\Exceptions\Manufacturer\ApiCallDoesNotSupportedException;
use App\Lib\IManufacturerAPI;
use App\Models\Meter\Meter;
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

    public function chargeMeter(Meter $meter, $energy): array
    {
        $timestamp = time();
        $cipherText = $this->generateCipherText(
            $meter->id,
            config('services.calin.user_id'),
            $meter->serial_number,
            'CreditToken',
            $energy,
            $timestamp);

        $tokenParams = [
            'serial_id' => $meter->id,
            'user_id' => config('services.calin.user_id'),
            'meter_id' => $meter->serial_number,
            'token_type' => 'CreditToken',
            'amount' => $energy,
            'timestamp' => $timestamp,
            'ciphertext' => $cipherText,
        ];
        return [
            'token' => $this->tokenRequest($tokenParams),
            'energy' => $energy
        ];
    }

    /**
     * Makes the external call to CALIN API and resturns the token.
     * @param $tokenParams
     * @return string
     * @throws Exception
     */
    private function tokenRequest($tokenParams): string
    {
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
            throw new Exception($tokenResult['reason']);

        }
        return $tokenResult['result'];
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


    public function clearMeter(Meter $meters)
    {
        throw  new ApiCallDoesNotSupportedException('This api call does not supported');
    }
}
