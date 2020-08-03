<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentCommissionRequest;
use App\Http\Resources\ApiResource;
use App\Models\AgentCommission;
use Illuminate\Http\Request;

class AgentCommissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $commissions = AgentCommission::query()->paginate(config('settings.paginate'));
        return new ApiResource($commissions);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAgentCommissionRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentCommissionRequest $request)
    {
        $commission = AgentCommission::query()->create(request()->only('name',
            'energy_commission',
            'appliance_commission',
            'risk_balance'));
        return new ApiResource($commission);
    }

    /**
     * Display the specified resourc
     *
     * @param AgentCommission $commission
     * @return void
     */
    public function show(AgentCommission $commission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Commission $commission
     * @return \Illuminate\Http\Response
     */
    public function edit(Commission $commission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Commission $commission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Commission $commission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Commission $commission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Commission $commission)
    {
        //
    }
}
