<?php


namespace App\Http\Controllers;


use App\Http\Resources\ApiResource;
use App\Models\Solar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Matrix\Exception;

class SolarController extends Controller
{
    /**
     * @var Solar
     */
    private $solar;

    public function __construct(Solar $solar)
    {
        $this->solar = $solar;
    }

    public function showByMiniGrid(Request $request, $id): ApiResource
    {
        try {
            $solarData = $this->solar::where('mini_grid_id', $id)
                ->latest()
                ->firstOrFail();
            return new ApiResource($solarData);
        } catch (ModelNotFoundException $exception) {
            return new ApiResource([]);
        }
    }

    public function store(Request $request): ApiResource
    { // Contribute KEmal 12.12.2019
        $miniGridId = $request->get('mini_grid_id');
        $solarDatas = $request->get('solar_reading');


        foreach ($solarDatas as $solarData) {

            $solar = $this->solar::create([
                'mini_grid_id' => $miniGridId,
                'node_id' => $request->get('node_id'),
                'device_id' => $request->get('device_id'),
                'start_time' => $solarData['start'],
                'end_time' => $solarData['end'],
                'min' => (int)$solarData['min'],
                'max' => (int)$solarData['max'],
                'average' => (int)$solarData['average'],
                'duration' => $solarData['duration'],
                'readings' => $solarData['total_readings'],
            ]);
        }
        //queue weather api

        event('solar.received', [
            'solar' => $solar,
            'mini_grid_id' => $miniGridId,
        ]);
        return new ApiResource($solar);
    }
}
