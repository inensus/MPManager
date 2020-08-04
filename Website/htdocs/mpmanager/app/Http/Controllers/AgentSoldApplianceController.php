<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentSoldApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\AgentSoldAppliance;
use App\Services\AgentSoldApplianceService;
use Illuminate\Http\Request;

class AgentSoldApplianceController extends Controller
{

    private $agentSoldApplianceService;

    public function __construct(AgentSoldApplianceService $agentSoldApplianceService)
    {
        $this->agentSoldApplianceService = $agentSoldApplianceService;
    }


    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $soldAppliances = $this->agentSoldApplianceService->list($agent->id);

        return new ApiResource($soldAppliances);
    }

    public function customerSoldAppliances($customerId, Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $soldAppliances = $this->agentSoldApplianceService->customerSoldList($customerId, $agent->id);

        return new ApiResource($soldAppliances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAgentSoldApplianceRequest $request
     * @return ApiResource
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param agentSoldAppliance $agent_sold_appliance
     * @return void
     */
    public function update(Request $request, agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\agentSoldAppliance $agent_sold_appliance
     * @return \Illuminate\Http\Response
     */
    public function destroy(agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    public function indexWeb(Agent $agent, Request $request)
    {

        $soldAppliances = $this->agentSoldApplianceService->listForWeb($agent->id);

        return new ApiResource($soldAppliances);
    }
}
