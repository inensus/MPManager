<?php


namespace App\Http\Controllers;


use App\Http\Resources\ApiResource;
use App\Models\Solar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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

    public function show(Request $request, $miniGridId)
    {
        $solarReadings = $this->solar->newQuery()
            ->where('mini_grid_id', $miniGridId);

        if ($startDate = $request->input('start_date')) {
            $solarReadings->where('time_stamp', '>=',
                Carbon::createFromTimestamp($startDate)->format('Y-m-d H:i:s'));
        }
        if ($endDate = $request->input('end_date')) {

            $solarReadings->where('time_stamp', '<=',
                Carbon::createFromTimestamp($endDate)->format('Y-m-d H:i:s')
            );
        }


        if ($request->input('weather')) {
            $solarReadings->with('weatherData');
        }


        return new ApiResource($solarReadings->get());
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
    { // Contribute Kemal 12.12.2019
        $miniGridId = $request->get('mini_grid_id');
        $solarDatas = $request->get('solar_reading');


        foreach ($solarDatas as $solarData) {

            $solar = $this->solar::create([
                'mini_grid_id' => $miniGridId,
                'node_id' => $request->get('node_id'),
                'device_id' => $request->get('device_id'),
                'starting_time' => $solarData['starting_time'],
                'ending_time' => $solarData['ending_time'],
                'min' => (int)$solarData['min'],
                'max' => (int)$solarData['max'],
                'average' => (int)$solarData['average'],
                'duration' => $solarData['duration'],
                'readings' => $solarData['total_readings'],
                'time_stamp' => $request->input('time_stamp'),
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
