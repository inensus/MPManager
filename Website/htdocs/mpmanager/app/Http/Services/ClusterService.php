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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * @return Builder|Builder[]|Collection|Model|null
     *
     * @psalm-return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|array<array-key, \Illuminate\Database\Eloquent\Builder>|null
     */
    public function getClusterCities($clusterId)
    {
        return Cluster::query()->with('cities')->find($clusterId);
    }

    /**
     * @return Builder|Builder[]|Collection|Model|null
     *
     * @psalm-return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|array<array-key, \Illuminate\Database\Eloquent\Builder>|null
     */
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
            $clusters[$index]['geo'] = [json_decode($clusterData, true)];
        }
        return $clusters;
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
            return $this->cluster->get();
        }
        return $this->cluster::with('miniGrids')->get();
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
            $city = $this->city->find($cityId);
            $cluster->cities()->save($city);
        }
    }
}
