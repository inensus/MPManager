<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetRate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Appliance Rates
 * Class AssetRateController
 * @package App\Http\Controllers
 */
class AssetRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param AssetRate $assetRate
     * @return Response
     */
    public function show(AssetRate $assetRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param AssetRate $assetRate
     * @return Response
     */
    public function edit(AssetRate $assetRate)
    {
        //
    }

    /**
     * Update
     * Update the specified rate.
     * @bodyParam asset_person_id int required
     * @bodyParam rate_cost int required
     * @bodyParam remaining int required
     * @bodyParam due_date date required
     * @param Request $request
     * @param AssetRate $assetRate
     * @return ApiResource
     */
    public function update(Request $request, AssetRate $assetRate): ApiResource
    {
        // notify log listener
        event('new.log', [
            'logData' => [
                'user_id' => $request->get('admin_id'),
                'affected' => $assetRate->assetPerson,
                'action' => 'Remaining rate ' . $assetRate->due_date . ' cost updated. From ' . $assetRate->remaining . ' to ' . $request->get('remaining')
            ]
        ]);


        $assetRate->remaining = $request->get('remaining');
        $assetRate->update();
        return new ApiResource($assetRate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AssetRate $assetRate
     * @return Response
     */
    public function destroy(AssetRate $assetRate)
    {
        //
    }
}
