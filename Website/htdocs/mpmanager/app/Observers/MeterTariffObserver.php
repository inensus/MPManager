<?php

namespace App\Observers;

use App\Jobs\TariffPricingComponentsCalculator;
use App\Models\AccessRate\AccessRate;
use App\Models\ElasticUsageTime;
use App\Models\Meter\MeterTariff;
use App\Models\SocialTariff;
use App\Models\TariffPricingComponent;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class MeterTariffObserver
{

    public function created(MeterTariff $tariff): void
    {
        if ($accessRate = request()->input('access_rate')) {
            AccessRate::create([
                'tariff_id' => $tariff->id,
                'amount' => $accessRate['access_rate_amount'],
                'period' => $accessRate['access_rate_period'],
            ]);
        }
        if ($social = request()->input('social_tariff')) {
            // create social tariff for the given tariff
            if ($social['daily_allowance'] != null & $social['price'] & $social['initial_energy_budget'] & $social['maximum_stacked_energy']) {
                SocialTariff::create([
                    'tariff_id' => $tariff->id,
                    'daily_allowance' => $social['daily_allowance'],
                    'price' => $social['price'],
                    'initial_energy_budget' => $social['initial_energy_budget'],
                    'maximum_stacked_energy' => $social['maximum_stacked_energy'],
                ]);
            }
        }
        if ($elasticUsageTimes = request()->input('elastic_usage_times')) {
            foreach ($elasticUsageTimes as $key => $value) {
                ElasticUsageTime::create([
                    'tariff_id' => $tariff->id,
                    'start' => $elasticUsageTimes[$key]['start'],
                    'end' => $elasticUsageTimes[$key]['end'],
                    'value' => $elasticUsageTimes[$key]['value']
                ]);
            }
        }
        if ($components = request()->input('components')) {

            TariffPricingComponentsCalculator::dispatch($tariff,
                $components)->allOnConnection('redis')->onQueue(config('services.queues.misc'));
        }

    }

    /**
     * The main job is to re-calculate the total tariff price.
     */
    /* public function updated(MeterTariff $tariff)
     {


     }*/
}
