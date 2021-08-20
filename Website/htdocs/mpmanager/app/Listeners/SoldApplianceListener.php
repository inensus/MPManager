<?php


namespace App\Listeners;


use App\Misc\SoldApplianceDataContainer;
use App\Models\AssetRate;
use App\Models\Person\Person;
use Carbon\Carbon;
use Illuminate\Events\Dispatcher;

class SoldApplianceListener
{
    /**
     * @var AssetRate
     */
    private $assetRate;

    public function __construct(AssetRate $assetRate)
    {
        $this->assetRate = $assetRate;
    }

    public function initializeApplianceRates(SoldApplianceDataContainer $soldAppliance):void
    {
        $assetPerson = $soldAppliance->getAssetPerson();
        $assetType = $soldAppliance->getAssetType();
        $transaction = $soldAppliance->getTransaction();
        $buyer = Person::query()->find($assetPerson->person_id);

        $base_time = $assetPerson->first_payment_date ?? date('Y-m-d');

        if ($assetPerson->down_payment > 0) {
            $this->assetRate::query()->create(
                [
                    'asset_person_id' => $assetPerson->id,
                    'rate_cost' => $assetPerson->down_payment,
                    'remaining' => 0,
                    'due_date' => Carbon::parse(date('Y-m-d'))->toIso8601ZuluString(),
                    'remind' => 0
                ]
            );
            $assetPerson->total_cost -= $assetPerson->down_payment;
        }

        foreach (range(1, $assetPerson->rate_count) as $rate) {
            if ($assetPerson->rate_count === 0) {
                $rate_cost = 0;
            } elseif ((int)$rate === (int)$assetPerson->rate_count) {
                //last rate
                $rate_cost = $assetPerson->total_cost
                    - (($rate - 1) * floor($assetPerson->total_cost / $assetPerson->rate_count));
            } else {
                $rate_cost = floor($assetPerson->total_cost / $assetPerson->rate_count);
            }
            $rate_date = date('Y-m-d', strtotime('+' . $rate . ' month', strtotime($base_time)));
            $this->assetRate::query()->create(
                [
                    'asset_person_id' => $assetPerson->id,
                    'rate_cost' => $rate_cost,
                    'remaining' => $rate_cost,
                    'due_date' => $rate_date,
                    'remind' => 0
                ]
            );
        }
        if($transaction === null){
            if( $assetPerson->down_payment > 0){
                event(
                    'payment.successful',
                    [
                        'amount' => $assetPerson->down_payment,
                        'paymentService' => 'web',
                        'paymentType' => 'appliance',
                        'sender' => $assetPerson->creator_id,
                        'paidFor' => $assetType,
                        'payer' => $buyer,
                        'transaction' => null,
                    ]
                );
            }
        }else{
            event(
                'payment.successful',
                [
                    'amount' => $transaction->amount,
                    'paymentService' => 'agent',
                    'paymentType' => 'appliance',
                    'sender' => $transaction->sender,
                    'paidFor' => $assetType,
                    'payer' => $buyer,
                    'transaction' => $transaction,
                ]
            );
        }

    }


    public function subscribe(Dispatcher $events):void
    {
        $events->listen('appliance.sold', 'App\Listeners\SoldApplianceListener@initializeApplianceRates');
    }

}
