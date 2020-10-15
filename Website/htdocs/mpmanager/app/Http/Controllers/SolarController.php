<?php


namespace App\Http\Controllers;


use App\Http\Requests\SolarCreateRequest;
use App\Http\Resources\ApiResource;
use App\Services\ISolarService;

/**
 * @group Solar
 * Class SolarController
 * @package App\Http\Controllers
 */
class SolarController extends Controller
{
    /**
     * @var ISolarService
     */
    private $solarService;

    public function __construct(ISolarService $solarService)
    {
        $this->solarService = $solarService;
    }

    /**
     * List
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $solarReadings = $this->solarService->list();
        return new ApiResource($solarReadings);
    }

    /**
     * List by Mini Grid
     * @param $miniGridId
     * @return ApiResource
     */
    public function listByMiniGrid($miniGridId)
    {
        echo "miniGridId " . $miniGridId;
        $solarReadings = $this->solarService->lisByMiniGrid($miniGridId);

        return new ApiResource($solarReadings);
    }

    /**
     * Detail by Mini Grid
     * @urlParam miniGridId int required
     * @param $miniGridId
     * @return ApiResource|\Illuminate\Http\JsonResponse
     */
    public function showByMiniGrid($miniGridId)
    {
        if ($reading = $this->solarService->showByMiniGrid($miniGridId)) {
            return new ApiResource($reading);
        }

        return response()->setStatusCode(404)->json(['data' => 'Nothing found']);
    }

    /**
     * Create
     * Create a new solar.
     * @bodyParam person_id int required
     * @bodyParam device_id int required
     * @bodyParam mini_grid_id int required
     * @bodyParam solar_reading.starting_time date required
     * @bodyParam solar_reading.readings int required
     * @bodyParam solar_reading.average int required
     * @bodyParam solar_reading.total int required
     * @bodyParam time_stamp int required
     * @param SolarCreateRequest $request
     * @return ApiResource
     */
    public function store(SolarCreateRequest $request): ApiResource
    { //unused parameter $request is needed for validation

        $solar = $this->solarService->create();

        return new ApiResource($solar);
    }
}
