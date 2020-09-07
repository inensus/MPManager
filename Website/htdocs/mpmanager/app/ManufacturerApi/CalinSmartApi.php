<?php


namespace App\ManufacturerApi;


use App\Lib\IManufacturerAPI;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterToken;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;
use Exception;

class CalinSmartApi implements IManufacturerAPI
{
    /**
     * @var Client
     */
    protected $api;

    public function __construct(Client $httpClient)
    {
        $this->api = $httpClient;
    }


    /**
     * @param Meter $meter
     * @param $energy
     * @return MeterToken
     * @throws Exception
     */
    public function chargeMeter(Meter $meter, $energy): array
    {

        $url = config('services.calinSmart.url.purchase');
        $tokenParams = [
            'company_name' => config('services.calinSmart.company_name'),
            'user_name' => config('services.calinSmart.user_name'),
            'password' => config('services.calinSmart.password'),
            'password_vend' => config('services.calinSmart.password_vend'),
            'meter_number' => $meter->serial_number,
            'is_vend_by_unit' => true,
            'amount' => $energy

        ];
        return [
            'token' => $this->tokenRequest($tokenParams, $url)['token'],
            'energy' => $energy
        ];
    }

    public function clearMeter(Meter $meter)
    {
        $url = config('services.calinSmart.url.clear');
        $tokenParams = [
            'company_name' => config('services.calinSmart.company_name'),
            'user_name' => config('services.calinSmart.user_name'),
            'password' => config('services.calinSmart.password'),
            'meter_number' => $meter->serial_number,
        ];
        return [
            'result_code' => $this->tokenRequest($tokenParams, $url)['result_code'],
        ];
    }

    /**
     * Makes the external call to CALIN API and resturns the token.
     * @param $tokenParams
     * @return string
     * @throws Exception
     */
    private function tokenRequest($tokenParams, $url)
    {
        $request = $this->api->post(
            $url,
            [
                'body' => json_encode($tokenParams),
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',

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


}
