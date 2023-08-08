<?php

namespace App\Http\Services;

use App\Http\Requests\TariffCreateRequest;
use App\Jobs\TariffPricingComponentsCalculator;
use App\Models\AccessRate\AccessRate;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\SocialTariff;
use App\Models\TariffPricingComponent;
use App\Models\TimeOfUsage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MeterTariffService
{
    public function update(MeterTariff $tariff, TariffCreateRequest $request): ?MeterTariff
    {
        if ($accessRate = request()->input('access_rate')) {
            if ($accessRate['id']) {
                $updatedAccessRate = AccessRate::query()->find($accessRate['id']);
                $updatedAccessRate->amount = $accessRate['access_rate_amount'];
                $updatedAccessRate->period = $accessRate['access_rate_period'];
                $updatedAccessRate->update();
            } else {
                AccessRate::query()->create([
                        'tariff_id' => $tariff->id,
                        'amount' => $accessRate['access_rate_amount'],
                        'period' => $accessRate['access_rate_period'],
                    ]);
            }
        } else {
            AccessRate::query()->where('tariff_id', $tariff->id)->delete();
        }
        if ($social = request()->input('social_tariff')) {
            if ($social['id']) {
                $updatedSocialTariff = SocialTariff::query()->find($social['id']);
                $updatedSocialTariff->daily_allowance = $social['daily_allowance'];
                $updatedSocialTariff->price = $social['price'];
                $updatedSocialTariff->initial_energy_budget = $social['initial_energy_budget'];
                $updatedSocialTariff->maximum_stacked_energy = $social['maximum_stacked_energy'];
                $updatedSocialTariff->update();
            } else {
                SocialTariff::query()->create(
                    [
                    'tariff_id' => $tariff->id,
                    'daily_allowance' => $social['daily_allowance'],
                    'price' => $social['price'],
                    'initial_energy_budget' => $social['initial_energy_budget'],
                    'maximum_stacked_energy' => $social['maximum_stacked_energy'],
                    ]
                );
            }
        } else {
            SocialTariff::query()->where('tariff_id', $tariff->id)->delete();
        }

        $pricingComponents = TariffPricingComponent::query()->where('owner_type', 'meter_tariff')->where(
            'owner_id',
            $tariff->id
        )->get();
        if ($pricingComponents) {
            foreach ($pricingComponents as $pricing) {
                TariffPricingComponent::query()->where('id', $pricing->id)->delete();
            }
        }
        if ($components = request()->input('components')) {
            TariffPricingComponentsCalculator::dispatch(
                $tariff,
                $components
            )->allOnConnection('redis')->onQueue(config('services.queues.misc'));
        }

        if ($timeOfUsages = request()->input('time_of_usage')) {
            foreach ($timeOfUsages as $timeOfUsage) {
                $tou = TimeOfUsage::query()->find($timeOfUsage['id']);
                if ($tou) {
                    $tou->start = $timeOfUsage['start'];
                    $tou->end = $timeOfUsage['end'];
                    $tou->value = $timeOfUsage['value'];
                    $tou->update();
                } else {
                    TimeOfUsage::query()->create(
                        [
                        'tariff_id' => $tariff->id,
                        'start' => $timeOfUsage['start'],
                        'end' => $timeOfUsage['end'],
                        'value' => $timeOfUsage['value']
                        ]
                    );
                }
            }
        }
        $tariff->update(
            [
            'name' => $request->input('name'),
            'factor' => $request->input('factor'),
            'currency' => $request->input('currency'),
            'price' => $request->input('price'),
            'total_price' => $request->input('price'),
             'updated_at' => date('Y-m-d h:i:s')
            ]
        );

        /** @var null|MeterTariff $meterTariff */
        $meterTariff = MeterTariff::with(
            [
            'accessRate',
            'pricingComponent',
            'socialTariff',
            'tou'
            ]
        )->find($tariff->id);

        return $meterTariff;
    }

    public function meterTariffUsageCount($tariffId): array
    {

        return DB::select(
            DB::raw(
                "select COUNT(meters.id) as count from meter_tariffs
               inner join meter_parameters on meter_tariffs.id=meter_parameters.tariff_id
               inner join meters on meter_parameters.meter_id=meters.id
               where meters.in_use =1 and meter_tariffs.id=$tariffId"
            )
        );
    }

    public function changeMetersTariff(int $currentId, int $changeId): Collection
    {
        $meterParameters = MeterParameter::query()->where('tariff_id', $currentId)->get();
        foreach ($meterParameters as $value) {
            $value->update(['tariff_id' => $changeId]);
        }
        return $meterParameters;
    }
}
