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
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inensus\Ticket\Trello\Api;
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
        if (Cache::store('redis')->has('cluster-list-data')) {
            return new ApiResource(Cache::store('redis')->get('cluster-list-data'));
        }
        $expiresAt = Carbon::now()->addMinutes(15);
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

        Cache::store('redis')->put('cluster-list-data', $this->fetchClusterData($clusters, $dateRange), $expiresAt);
        return new ApiResource($this->fetchClusterData($clusters, $dateRange));
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
        }

        $cluster['geo'] = json_decode($clusterData);
        return new ApiResource($cluster);
    }
    /**
     * Gives the json files back which contains the polygon of the given cluster
     */
    public function geo()
    {
        $clusters = $this->clusterService->getClusterList();
        foreach ($clusters as $index => $cluster) {
            try {
                $clusterData = Storage::disk('local')->get($cluster->name . '.json');
            } catch (FileNotFoundException $e) {
                continue;
            }

            $clusters[$index]['geo'] = [json_decode($clusterData)];
        }
        return new ApiResource($clusters);
    }

    private function fetchClusterData($clusters, $range = [])
    {
        foreach ($clusters as $index => $cluster) {
            $cities = $this->clusterService->getClusterCities($cluster);
            foreach ($cities as $cityIndex => $city) {
                $city = $this->cityService->getCityPopulation($city);
                $city = $this->meterService->getMetersInCity($city);
                $city->revenue = $this->transactionService->totalMeterTransactions($city->meters, $range);
                //unset meter list, coz its not needed
                unset($city->meters);

                $cities[$cityIndex] = $city;
            }
            $clusters[$index]->cities = $cities;
        }
        return $clusters;
    }


    public function store(ClusterRequest $request, Response $response)
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


        return new ApiResource(
            $this->cluster
        );

    }
}
