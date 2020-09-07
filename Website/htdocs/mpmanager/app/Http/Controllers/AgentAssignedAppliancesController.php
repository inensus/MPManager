<?php

namespace App\Http\Controllers;
use App\Http\Requests\CreateAgentAssignedApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentAssignedApplianceService;
use Illuminate\Http\Request;

class AgentAssignedAppliancesController extends Controller
{

    private $agentAssignedApplianceService;

    public function __construct(AgentAssignedApplianceService $AgentAssignedApplianceService)
    {
        $this->agentAssignedApplianceService = $AgentAssignedApplianceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $assignedAppliances = $this->agentAssignedApplianceService->list($agent->id);
        return new ApiResource($assignedAppliances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateAgentAssignedApplianceRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentAssignedApplianceRequest $request)
    {

        $appliance = $this->agentAssignedApplianceService->create($request->only([
            'agent_id',
            'user_id',
            'appliance_type_id',
            'cost',
        ]));

        return new ApiResource($appliance);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function indexWeb(Agent $agent, Request $request)
    {

        $assignedAppliances = $this->agentAssignedApplianceService->list($agent->id);
        return new ApiResource($assignedAppliances);
    }
}
