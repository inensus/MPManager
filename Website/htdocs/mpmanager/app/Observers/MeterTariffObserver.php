<?php

namespace App\Observers;

use App\Jobs\TariffPricingComponentsCalculator;
use App\Models\AccessRate\AccessRate;
use App\Models\Meter\MeterTariff;
use App\Models\SocialTariff;
use Illuminate\Support\Facades\Log;

class MeterTariffObserver
{

    public function created(MeterTariff $tariff): void
    {
        if (request()->has('access_rate_amount') && request()->has('access_rate_period')) {
            //append access-rate for the output
            AccessRate::create([
                'tariff_id' => $tariff->id,
                'amount' => request()->input('access_rate_amount'),
                'period' => request()->input('access_rate_period'),
            ]);
        }
        if ($components = request()->input('components')) {
            TariffPricingComponentsCalculator::dispatch($tariff,
                $components)->allOnConnection('redis')->onQueue(config('services.queues.misc'));
        }
        if ($social = request()->input('social_tariff')) {
            // create social tariff for the given tariff
            SocialTariff::create([
                'tariff_id' => $tariff->id,
                'daily_allowance' => $social['daily_allowance'],
                'price' => $social['price'],
                'initial_energy_budget' => $social['initial_energy_budget'],
                'maximum_stacked_energy' => $social['maximum_stacked_energy'],
            ]);
        }
    }

    /**
     * The main job is to re-calculate the total tariff price.
     */
    public function updated()
    {
//recalculate tariff total price
    }
}
