<?php


namespace App\ManufacturerApi;


use App\Lib\IManufacturerAPI;
use App\Misc\TransactionDataContainer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterToken;
use App\Models\Transaction\Transaction;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;
use Exception;

class CalinSmartApi implements IManufacturerAPI
{
    /**
     * @var Client
     */
    protected $api;
    private $transaction;
    public function __construct(Client $httpClient,
        Transaction $transaction)
    {
        $this->api = $httpClient;
        $this->transaction=$transaction;
    }


    /**
     * @param $transactionContainer
     * @return array
     * @throws Exception
     */
    public function chargeMeter($transactionContainer): array
    {
        $meterParameter = $transactionContainer->meterParameter;
        $transactionContainer->chargedEnergy += $transactionContainer->amount / ($meterParameter->tariff()->first()->total_price / 100);
        Log::critical('ENERGY TO BE CHARGED float ' . (float)$transactionContainer->chargedEnergy.' Manufacturer => Calin Smart');

        if (config('app.debug')) {
            return [
                'token' => 'debug-token',
                'energy' => (float)$transactionContainer->chargedEnergy,
            ];
        }else{
            $meter = $transactionContainer->meter;
            $energy = (float)$transactionContainer->chargedEnergy;
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
            $transactionResult = $this->tokenRequest($tokenParams, $url);

            return [
                'token' => $transactionResult['token'],
                'energy' => $energy
            ];
        }

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
    private function tokenRequest($tokenParams, $url):Array
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


    public function associateManufacturerTransaction(TransactionDataContainer $transactionContainer)
    {
        $transaction =$this->transaction->newQuery()->with('originalAirtel', 'originalVodacom','orginalAgent','originalThirdParty')->find($transactionContainer->transaction->id);
        if ($transaction->originalAirtel){
            $transaction->originalAirtel->update([
                'manufacturer_transaction_type'=>'calin_transaction',
                'manufacturer_transaction_id'=>$transaction->id
            ]);
            $transaction->originalAirtel->save();

        }else if($transaction->originalVodacom){

            $transaction->originalVodacom->update([
                'manufacturer_transaction_type'=>'calin_transaction',
                'manufacturer_transaction_id'=>$transaction->id
            ]);
            $transaction->originalVodacom->save();

        }else if($transaction->orginalAgent){

            $transaction->orginalAgent->update([
                'manufacturer_transaction_type'=>'calin_transaction',
                'manufacturer_transaction_id'=>$transaction->id
            ]);
            $transaction->orginalAgent->save();

        }else if($transaction->originalThirdParty){

            $transaction->originalThirdParty->update([
                'manufacturer_transaction_type'=>'calin_transaction',
                'manufacturer_transaction_id'=>$transaction->id
            ]);
            $transaction->originalThirdParty->save();
        }
    }
}
