<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;

use App\Http\Resources\MeterTypeResource;
use App\Models\Meter\MeterType;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group MeterTypes
 * Class MeterTypeController
 * @package App\Http\Controllers
 */
class MeterTypeController extends Controller
{
    use SoftDeletes;

    /**
     * List
     *
     * @responseFile responses/metertypes/meter.types.list.json
     */
    public function index()
    {
        return new ApiResource(
            MeterType::all()
        );
    }


    /**
     * Store
     * Creates a new meter type
     *
     * @bodyParam online int required
     * @bodyParam phase int required
     * @bodyParam max_current int required
     *
     * @param Request $request
     * @return ApiResource
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        //validate
        $validation = Validator::make($request->all(), MeterType::$rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
        return
            new ApiResource(
                MeterType::create(
                    request()->only([
                        'online',
                        'phase',
                        'max_current',
                    ])
                )
            );
    }

    /**
     * Detail
     * @bodyParam id int required
     *
     * @param int $id
     * @return ApiResource
     */
    public function show($id)
    {
        return new ApiResource(
            MeterType::findOrFail($id)
        );
    }


    /**
     * Update
     * Updates the given meter type
     * @urlParam id required
     * @bodyParam online int required
     * @bodyParam phase int required
     * @bodyParam max_current int required
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        //validate
        $validation = Validator::make($request->all(), MeterType::$rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $meter_type = MeterType::findOrFail($id);
        $meter_type->online = request('online');
        $meter_type->phase = request('phase');
        $meter_type->max_current = request('max_current');
        $meter_type->save();
        return new ApiResource($meter_type);
    }

    /**
     * List with Meters
     * Displays the meter types with the associated meters
     * @urlParam id required
     *
     * @responseFile responses/metertypes/metertypes.meter.list.json
     * @param Request $request
     * @param $id
     * @return ApiResource
     */
    public function meterList(Request $request, $id)
    {
        return new ApiResource(
            MeterType::with('meters')->findOrFail($id)
        );
    }

}
