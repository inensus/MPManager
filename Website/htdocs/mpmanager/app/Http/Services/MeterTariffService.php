<?php


namespace App\Http\Services;


use App\Http\Requests\TariffCreateRequest;
use App\Jobs\TariffPricingComponentsCalculator;
use App\Models\AccessRate\AccessRate;
use App\Models\ElasticUsageTime;
use App\Models\Meter\MeterTariff;
use App\Models\SocialTariff;
use App\Models\TariffPricingComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MeterTariffService
{


    public function update(MeterTariff $tariff, TariffCreateRequest $request):Model
    {
        $tariff->name = $request->input('name');
        $tariff->factor = $request->input('factor');
        $tariff->currency = $request->input('currency');
        $tariff->price = $request->input('price');
        $tariff->total_price = $request->input('price');
        $tariff->update();

        if ($accessRate = request()->input('access_rate')) {
            if ($accessRate['id']) {
                $updatedAccessRate = AccessRate::find($accessRate['id']);
                $updatedAccessRate->amount = $accessRate['access_rate_amount'];
                $updatedAccessRate->period = $accessRate['access_rate_period'];
                $updatedAccessRate->update();
            } else {
                AccessRate::create([
                    'tariff_id' => $tariff->id,
                    'amount' => $accessRate['access_rate_amount'],
                    'period' => $accessRate['access_rate_period'],
                ]);
            }

        } else {
            AccessRate::where('tariff_id', $tariff->id)->delete();
        }
        if ($social = request()->input('social_tariff')) {
            if ($social['id']) {
                $updatedSocialTariff = SocialTariff::find($social['id']);
                $updatedSocialTariff->daily_allowance = $social['daily_allowance'];
                $updatedSocialTariff->price = $social['price'];
                $updatedSocialTariff->initial_energy_budget = $social['initial_energy_budget'];
                $updatedSocialTariff->maximum_stacked_energy = $social['maximum_stacked_energy'];
                $updatedSocialTariff->update();
            } else {
                SocialTariff::create([
                    'tariff_id' => $tariff->id,
                    'daily_allowance' => $social['daily_allowance'],
                    'price' => $social['price'],
                    'initial_energy_budget' => $social['initial_energy_budget'],
                    'maximum_stacked_energy' => $social['maximum_stacked_energy'],
                ]);
            }

        } else {
            SocialTariff::where('tariff_id', $tariff->id)->delete();
        }

        $pricingComponents = TariffPricingComponent::where('owner_type', 'meter_tariff')->where('owner_id',
            $tariff->id)->get();
        if ($pricingComponents) {
            foreach ($pricingComponents as $key => $pricing) {
                TariffPricingComponent::where('id', $pricing->id)->delete();
            }
        }
        if ($components = request()->input('components')) {
            TariffPricingComponentsCalculator::dispatch($tariff,
                $components)->allOnConnection('redis')->onQueue(config('services.queues.misc'));
        }

        if ($elasticUsageTimes = request()->input('elastic_usage_times')) {
            foreach ($elasticUsageTimes as $key => $value) {
                $elasticUsage = ElasticUsageTime::find($elasticUsageTimes[$key]['id']);
                if ($elasticUsage) {
                    $elasticUsage->start = $elasticUsageTimes[$key]['start'];
                    $elasticUsage->end = $elasticUsageTimes[$key]['end'];
                    $elasticUsage->value = $elasticUsageTimes[$key]['value'];
                    $elasticUsage->update();
                } else {
                    ElasticUsageTime::create([
                        'tariff_id' => $tariff->id,
                        'start' => $elasticUsageTimes[$key]['start'],
                        'end' => $elasticUsageTimes[$key]['end'],
                        'value' => $elasticUsageTimes[$key]['value']
                    ]);
                }
            }
        }
     return   $meterTariff = MeterTariff::with([
            'accessRate',
            'pricingComponent',
            'socialTariff',
            'elasticUsageTime'
        ])->find($tariff->id);
    }

    public function meterTariffUsageCount($tariffId)
    {

        return DB::select(DB::raw("select COUNT(meters.id) as count from meter_tariffs
               inner join meter_parameters on meter_tariffs.id=meter_parameters.tariff_id
               inner join meters on meter_parameters.meter_id=meters.id
               where meters.in_use =1 and meter_tariffs.id=$tariffId"));

    }
}
