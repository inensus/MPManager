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
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

class ClusterService
{


    /**
     * @var Cluster
     */
    private $cluster;
    /**
     * @var City
     */
    private $city;

    private $meterService;

    private $transactionService;

    private $cityService;

    /**
     * ClusterService constructor.
     *
     * @param Cluster            $cluster
     * @param City               $city
     * @param MeterService       $meterService
     * @param TransactionService $transactionService
     * @param CityService        $cityService
     */
    public function __construct(
        Cluster $cluster,
        City $city,
        MeterService $meterService,
        TransactionService $transactionService,
        CityService $cityService
    ) {
        $this->meterService = $meterService;
        $this->cluster = $cluster;
        $this->city = $city;
        $this->cityService = $cityService;
        $this->transactionService = $transactionService;
    }

    public function getClusterCities($clusterId)
    {
        return Cluster::query()->with('cities')->find($clusterId);
    }

    public function getClusterMiniGrids($clusterId)
    {
        return Cluster::query()->with('miniGrids')->find($clusterId);
    }

    public function fetchClusterGeoJson($clusters)
    {
        foreach ($clusters as $index => $cluster) {
            try {
                $clusterData = Storage::disk('local')->get($cluster->name . '.json');
            } catch (FileNotFoundException $e) {
                continue;
            }

            $clusters[$index]['geo'] = [json_decode($clusterData)];
        }
        return $clusters;
    }

    public function fetchClusterData($clusters, $range = [])
    {

        foreach ($clusters as $index => $cluster) {
            $clusters[$index]->meterCount = $this->meterService->getMeterCountInCluster($cluster->id);
            $clusters[$index]->revenue = $this->transactionService->totalClusterTransactions($cluster->id, $range);
            $clusters[$index]->population = $this->cityService->getClusteropulation($cluster->id);
        }
        return $clusters;
    }

    public function getClusterList($withCities = false)
    {
        if (!$withCities) {
            return $this->cluster->get();
        }
        return $this->cluster::with('miniGrids')->get();
    }

    public function getClustersCities($clusters, $callback)
    {
        foreach ($clusters as $cluster) {
            $callback($cluster->cities()->get());
        }
    }

    public function attachCities(Cluster $cluster, $cities): void
    {
        foreach ($cities as $cityId) {
            $city = $this->city->find($cityId);
            $cluster->cities()->save($city);
        }
    }
}
