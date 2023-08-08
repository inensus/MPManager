<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 17:34
 */

namespace App\Http\Services;

use App\Models\City;
use App\Models\Meter\Meter;
use Exception;

use Illuminate\Database\Eloquent\Collection;

use function count;

class MeterService
{
    public function __construct(private Meter $meter)
    {
    }


    public function getMeterCountInCluster(int $clusterId): int
    {
        return $this->meter->newQuery()
            ->whereHas(
            'meterParameter',
            function ($q) use ($clusterId) {
                $q->whereHas(
                    'address',
                    function ($q) use ($clusterId) {
                        $q->whereHas(
                            'city',
                            function ($q) use ($clusterId) {
                                $q->where('cluster_id', $clusterId);
                            }
                        );
                    }
                );
            }
        )->count();
    }

    public function getMetersInCity(City $city): City
    {
        $cityId = $city->id;
        $meters = $this->meter->newQuery()
        ->whereHas(
            'meterParameter',
            function ($q) use ($cityId) {
                $q->whereHas(
                    'address',
                    function ($q) use ($cityId) {
                        $q->where('city_id', $cityId);
                    }
                );
            }
        )->get();
        $city['meters'] = $meters;
        $city['metersCount'] = count($meters);
        return $city;
    }

    public function getMetersInMiniGrid(int $miniGridId): Collection
    {
        return $this->meter->newQuery()
            ->whereHas(
            'meterParameter',
            static function ($q) use ($miniGridId) {
                //meter.meter_parameter
                $q->whereHas(
                    'address',
                    function ($q) use ($miniGridId) {
                        //meter.meter_parameter.address
                        $q->whereHas(
                            'city',
                            function ($q) use ($miniGridId) {
                                //meter.meter_parameter.address.city
                                $q->where('mini_grid_id', $miniGridId);
                            }
                        );
                    }
                );
            }
        )->get();
    }

    public function updateMeterGeoLocations(array $meters): array
    {
        try {
            foreach ($meters as $meter) {
                $points = [
                    $meter['lat'],
                    $meter['lng']
                ];
                if ($points) {
                    $meter = $this->meter->newQuery()->find($meter['id']);
                    $geo = $meter->meterParameter()->first()->address()->first()->geo()->first();
                    $geo->points = $points[0] . ',' . $points[1];
                    $geo->save();
                }
            }
            return ['data' => true];
        } catch (Exception $exception) {
             throw  new Exception($exception->getMessage());
        }
    }
}
