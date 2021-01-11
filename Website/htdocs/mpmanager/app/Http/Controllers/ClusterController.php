<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-12
 * Time: 11:45
 */

namespace App\Http\Controllers;


use App\Events\ClusterEvent;
use App\Http\Requests\ClusterRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\CityService;
use App\Http\Services\ClusterService;
use App\Http\Services\MeterService;
use App\Http\Services\TransactionService;
use App\Models\Cluster;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use function json_decode;

class ClusterController
{
    /**
     * @var ClusterService
     */
    private $clusterService;
    /**
     * @var CityService
     */
    private $cityService;
    /**
     * @var MeterService
     */
    private $meterService;
    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * @var Cluster
     */
    private $cluster;

    /**
     * ClusterController constructor.
     *
     * @param ClusterService $clusterService
     * @param CityService $cityService
     * @param MeterService $meterService
     * @param TransactionService $transactionService
     * @param Cluster $cluster
     */
    public function __construct(
        ClusterService $clusterService,
        CityService $cityService,
        MeterService $meterService,
        TransactionService $transactionService,
        Cluster $cluster
    ) {
        $this->clusterService = $clusterService;
        $this->cityService = $cityService;
        $this->meterService = $meterService;
        $this->transactionService = $transactionService;
        $this->cluster = $cluster;
    }

    public function index()
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

        $clusters = $this->clusterService->fetchClusterGeoJson($clusters);

        return new ApiResource($this->clusterService->fetchClusterData($clusters, $dateRange));
    }

    public function show($id)
    {

        $cluster = $this->cluster::with('miniGrids.location')
            ->find($id);
        return new ApiResource($cluster);
    }

    public function showGeo(Cluster $cluster)
    {
        try {
            $clusterData = Storage::disk('local')->get($cluster->name . '.json');
        } catch (FileNotFoundException $e) {
            return new ApiResource([]);
        }

        $cluster['geo'] = json_decode($clusterData);
        return new ApiResource($cluster);
    }


    public function store(ClusterRequest $request)
    {
        //type of geo data its either remote or manual
        $geoType = $request->get('geo_type');
        //holds coordinates or external url which contains a geojson. Depends on geoType
        $geoData = $request->get('geo_data');
        $name = $request->get('name');
        //a list of city ids which will be attached to the new cluster
        $cities = $request->get('cities');
        //the person who has the responsibility for that cluster
        $managerId = $request->get('manager_id');

        //save cluster
        $this->cluster->name = $name;
        $this->cluster->manager_id = $managerId;
        $this->cluster->save();

        //fire the create geo-json event. It creates a json file with coordinates
        event(new ClusterEvent($this->cluster, $geoType, $geoData));

        return new ApiResource($this->cluster);

    }
}
