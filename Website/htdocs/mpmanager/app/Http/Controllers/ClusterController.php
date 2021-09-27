<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-12
 * Time: 11:45
 */

namespace App\Http\Controllers;

use App\Http\Requests\ClusterRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\ClusterService;
use App\Models\Cluster;

/**
 * @group   Cluster
 * Class ClusterController
 * @package App\Http\Controllers
 */

class ClusterController
{
    /**
     * @var ClusterService
     */
    private $clusterService;
    /**
     * @var Cluster
     */
    private $cluster;

    /**
     * ClusterController constructor.
     *
     * @param ClusterService     $clusterService
     * @param Cluster            $cluster
     */
    public function __construct(
        ClusterService $clusterService,
        Cluster $cluster
    ) {
        $this->clusterService = $clusterService;
        $this->cluster = $cluster;
    }

    /**
     * List of Clusters
     * A list of all clusters.
     * @responseFile responses/cluster/clusters.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $startDate = request('start_date');
        $endDate = request('end_date');
        $dateRange = [];
        if ($startDate !== null && $endDate !== null) {
            $dateRange[0] = $startDate;
            $dateRange[1] = $endDate;
        } else {
            $dateRange[0] = date('Y-m-d', strtotime('today - 31 days'));
            $dateRange[1] = date('Y-m-d', strtotime('today - 1 days'));
        }
        $clusters = $this->clusterService->getClusterList();

        return new ApiResource($this->clusterService->fetchClusterData($clusters, $dateRange));
    }

    /**
     * Details of specified cluster.
     * @urlParam clusterId required
     * @responseFile responses/cluster/cluster.detail.json
     * @param $id
     * @return ApiResource
     */
    public function show($id): ApiResource
    {
        $cluster = Cluster::with('miniGrids.location')->find($id);
        return new ApiResource($cluster);
    }

    /**
     * Show Geo
     * Show geographical information of specified cluster.
     * @urlParam clusterId required
     * @responseFile responses/cluster/cluster.geo.information.json
     * @param $id
     * @return ApiResource
     */
    public function showGeo($id): ApiResource
    {
        return ApiResource::make($this->clusterService->geoLocation($id));
    }

    /**
     * Create a new cluster.
     *
     * @bodyParam name string required
     * @bodyParam manager_id int required
     * @bodyParam geo_data json required
     * @param ClusterRequest $request
     * @return ApiResource
     */
    public function store(ClusterRequest $request): ApiResource
    {
        $cluster = Cluster::query()->create(
            request()->only(['name', 'manager_id', 'geo_data'])
        );
        return new ApiResource($cluster);
    }
}
