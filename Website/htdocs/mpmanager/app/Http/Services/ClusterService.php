<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 19:19
 */

namespace App\Http\Services;

use App\Models\Cluster;
use App\Services\ClusterRevenueService;
use Illuminate\Database\Eloquent\Collection;

class ClusterService
{
    public function __construct(
        private MeterService $meterService,
        private TransactionService $transactionService,
        private CityService $cityService,
        private ClusterRevenueService $clusterRevenueService,
    ) {
    }

    public function getCluster(int $id): ?Cluster
    {
        $dateRange[0] = date('Y-m-d', strtotime('today - 31 days'));
        $dateRange[1] = date('Y-m-d', strtotime('today - 1 days'));
        /** @var null|Cluster $cluster */
        $cluster = Cluster::with('miniGrids.location')->find($id);
        $cluster->meterCount = $this->meterService->getMeterCountInCluster($cluster->id);
        $cluster->revenue = $this->transactionService->totalClusterTransactions($cluster->id, $dateRange);
        $cluster->population = $this->cityService->getClusterPopulation($cluster->id);
        return $cluster;
    }

    public function getClusterMiniGrids(int $clusterId): ?Cluster
    {
        /** @var null|Cluster $cluster */
        $cluster = Cluster::query()->with('miniGrids')->find($clusterId);

        return $cluster;
    }

    public function fetchClusterData(Collection $clusters, array $range = []): Collection
    {
        foreach ($clusters as $index => $cluster) {
            $clusters[$index]->meterCount = $this->meterService->getMeterCountInCluster($cluster->id);
            $clusters[$index]->revenue = $this->transactionService->totalClusterTransactions($cluster->id, $range);
            $clusters[$index]->population = $this->cityService->getClusterPopulation($cluster->id);
            $clusters[$index]->citiesRevenue = $this->clusterRevenueService->getMiniGridBasedRevenue($cluster->id);
            $clusters[$index]->revenueAnalysis = $this->clusterRevenueService->getRevenueAnalysis($cluster->id);
            $clusters[$index]->clusterData = $this->getCluster($cluster->id);
        }
        return $clusters;
    }

    public function getClusterList(bool $withCities = false): Collection
    {
        if (!$withCities) {
            return Cluster::query()->get();
        }
        return Cluster::query()->with('miniGrids')->get();
    }

    public function geoLocation($clusterId): ?string
    {
        $cluster = Cluster::select('geo_data')->find($clusterId);
        return $cluster->geo_data;
    }
}
