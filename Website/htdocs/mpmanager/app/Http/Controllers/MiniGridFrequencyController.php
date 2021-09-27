<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MiniGridFrequency;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   MiniGrid Frequency
 * Class MiniGridController
 * @package App\Http\Controllers
 */
class MiniGridFrequencyController extends Controller
{

    /**
     * Create
     * Create mini grid frequency.
     * @bodyParam mini_grid_id int
     * @bodyParam node_id int
     * @bodyParam device_id int
     * @bodyParam frequency string
     * @bodyParam frequency_unit string
     * @bodyParam time_stamp date
     * @param  Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $gridData = $request->input('grid');

        $frequency = MiniGridFrequency::make(
            [
            'mini_grid_id' => $request->input('mini_grid_id'),
            'node_id' => $request->input('node_id'),
            'device_id' => $request->input('device_id'),
            'frequency' => str_replace(',', '.', $gridData['value']),
            'frequency_unit' => $gridData['unit'],
            'time_stamp' => date('Y-m-d H:i:s', strtotime($gridData['time_stamp'])),

            ]
        );
        $frequency->save();
        return new ApiResource($frequency);
    }
}
