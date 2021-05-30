<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 19:19
 */

namespace App\Http\Services;

use App\Models\City;
use App\Models\Cluster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ClusterService
{


    private $meterService;

    private $transactionService;

    private $cityService;

    /**
     * ClusterService constructor.
     *
     * @param MeterService       $meterService
     * @param TransactionService $transactionService
     * @param CityService        $cityService
     */
    public function __construct(
        MeterService $meterService,
        TransactionService $transactionService,
        CityService $cityService
    ) {
        $this->meterService = $meterService;
        $this->cityService = $cityService;
        $this->transactionService = $transactionService;
    }

    /**
     * @param $clusterId
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getClusterCities($clusterId)
    {
        return Cluster::query()->with('cities')->find($clusterId);
    }

    /**
     * @param $clusterId
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function getClusterMiniGrids($clusterId)
    {
        return Cluster::query()->with('miniGrids')->find($clusterId);
    }

    /**
     * @param (\Illuminate\Http\Request|array|string)[] $range
     * @param array $range
     * @return mixed
     */
    public function fetchClusterData($clusters, array $range = [])
    {
        foreach ($clusters as $index => $cluster) {
            $clusters[$index]->meterCount = $this->meterService->getMeterCountInCluster($cluster->id);
            $clusters[$index]->revenue = $this->transactionService->totalClusterTransactions($cluster->id, $range);
            $clusters[$index]->population = $this->cityService->getClusteropulation($cluster->id);
        }
        return $clusters;
    }

    public function getClusterList(bool $withCities = false)
    {
        if (!$withCities) {
            return Cluster::query()->get();
        }
        return Cluster::query()->with('miniGrids')->get();
    }

    public function getClustersCities($clusters, $callback): void
    {
        foreach ($clusters as $cluster) {
            $callback($cluster->cities()->get());
        }
    }

    public function attachCities(Cluster $cluster, $cities): void
    {
        foreach ($cities as $cityId) {
            $city = City::query()->find($cityId);
            $cluster->cities()->save($city);
        }
    }

    public function geoLocation($clusterId)
    {
        $cluster = Cluster::select('geo_data')->find($clusterId);
        return $cluster->geo_data;
    }
}
