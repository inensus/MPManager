<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentSoldApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentSoldApplianceService;
use Illuminate\Http\Request;

class AgentSoldApplianceController extends Controller
{

    private  $agentSoldApplianceService;

    public function __construct(AgentSoldApplianceService $agentSoldApplianceService)
    {
        $this->agentSoldApplianceService =$agentSoldApplianceService;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function index(Agent $agent, Request $request)
    {
        $soldAppliances = $this->agentSoldApplianceService->list($agent->id);

        return new ApiResource($soldAppliances);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentSoldApplianceRequest $request)
    {

        $appliance = $this->agentSoldApplianceService->create($request->only([
            'person_id',
            'agent_assigned_appliance_id',

        ]));

        return new ApiResource($appliance);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\agentSoldAppliance  $agent_sold_appliance
     * @return \Illuminate\Http\Response
     */
    public function show(agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\agentSoldAppliance  $agent_sold_appliance
     * @return \Illuminate\Http\Response
     */
    public function edit(agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\agentSoldAppliance  $agent_sold_appliance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\agentSoldAppliance  $agent_sold_appliance
     * @return \Illuminate\Http\Response
     */
    public function destroy(agentSoldAppliance $agent_sold_appliance)
    {
        //
    }
}
