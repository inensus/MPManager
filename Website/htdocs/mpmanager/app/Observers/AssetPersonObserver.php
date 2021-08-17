<?php

namespace App\Observers;

use App\Models\AssetPerson;
use App\Models\AssetRate;
use Carbon\Carbon;

class AssetPersonObserver
{
    /**
     * @var AssetRate
     */
    private $assetRate;

    public function __construct(AssetRate $assetRate)
    {
        $this->assetRate = $assetRate;
    }

    /**
     * Handle the asset person "created" event.
     *
     * @param AssetPerson $assetPerson
     * @return void
     */
    public function created(AssetPerson $assetPerson): void
    {
        $base_time = $assetPerson->first_payment_date ?? date('Y-m-d');

        if ($assetPerson->down_payment > 0) {
            $this->assetRate::create(
                [
                    'asset_person_id' => $assetPerson->id,
                    'rate_cost' => $assetPerson->down_payment,
                    'remaining' => 0,
                    'due_date' => Carbon::parse(date('Y-m-d'))->toIso8601ZuluString(),
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
            $this->assetRate::create(
                [
                    'asset_person_id' => $assetPerson->id,
                    'rate_cost' => $rate_cost,
                    'remaining' => $rate_cost,
                    'due_date' => $rate_date
                ]
            );
        }
    }
}
