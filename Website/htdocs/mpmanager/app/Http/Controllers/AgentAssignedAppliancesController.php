<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentAssignedApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentAssignedApplianceService;
use Illuminate\Http\Request;

/**
 * @group   Agent Assigned Appliances
 * Class AgentAssignedApplianceController
 * @package App\Http\Controllers
 */
class AgentAssignedAppliancesController extends Controller
{

    private $agentAssignedApplianceService;

    public function __construct(AgentAssignedApplianceService $AgentAssignedApplianceService)
    {
        $this->agentAssignedApplianceService = $AgentAssignedApplianceService;
    }

    /**
     * List assigned appliances for Android-APP.
     * @responseFile responses/agent/agent.assigned.appliances.json
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $assignedAppliances = $this->agentAssignedApplianceService->list($agent->id);
        return new ApiResource($assignedAppliances);
    }

    /**
     * Create a new assigned appliance.
     *
     * @bodyParam agent_id integer required
     * @bodyParam user_id integer required
     * @bodyParam appliance_type_id integer required
     * @bodyParam cost integer required
     * @param     CreateAgentAssignedApplianceRequest $request
     * @return    ApiResource
     */
    public function store(CreateAgentAssignedApplianceRequest $request)
    {

        $appliance = $this->agentAssignedApplianceService->create(
            $request->only(
                [
                'agent_id',
                'user_id',
                'appliance_type_id',
                'cost',
                ]
            )
        );

        return new ApiResource($appliance);
    }




    /**
     * List for Web interface.
     * @responseFile responses/agent/agent.assigned.appliances.json
     * @param  Agent   $agent
     * @param  Request $request
     * @return ApiResource
     */
    public function indexWeb(Agent $agent, Request $request)
    {

        $assignedAppliances = $this->agentAssignedApplianceService->list($agent->id);
        return new ApiResource($assignedAppliances);
    }
}
