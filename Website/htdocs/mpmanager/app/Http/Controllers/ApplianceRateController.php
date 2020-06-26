<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ApplianceRate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApplianceRateController extends Controller
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
     * @param ApplianceRate $applianceRate
     * @return Response
     */
    public function show(ApplianceRate $applianceRate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ApplianceRate $applianceRate
     * @return Response
     */
    public function edit(ApplianceRate $applianceRate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ApplianceRate $applianceRate
     * @return ApiResource
     */
    public function update(Request $request, ApplianceRate $applianceRate): ApiResource
    {
        // notify log listener
        event('new.log', [
            'logData' => [
                'user_id' => $request->get('admin_id'),
                'affected' => $applianceRate->appliancePerson,
                'action' => 'Remaining rate ' . $applianceRate->due_date . ' cost updated. From ' . $applianceRate->remaining . ' to ' . $request->get('remaining')
            ]
        ]);


        $applianceRate->remaining = $request->get('remaining');
        $applianceRate->update();
        return new ApiResource($applianceRate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApplianceRate $applianceRate
     * @return Response
     */
    public function destroy(ApplianceRate $applianceRate)
    {
        //
    }
}
