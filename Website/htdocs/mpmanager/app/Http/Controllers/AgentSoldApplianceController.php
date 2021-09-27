<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentSoldApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\AgentSoldAppliance;
use App\Services\AgentSoldApplianceService;
use Illuminate\Http\Request;

/**
 * @group   Agent Sold Appliance
 * Class AgentSoldApplianceController
 * @package App\Http\Controllers
 */

class AgentSoldApplianceController extends Controller
{

    private $agentSoldApplianceService;

    public function __construct(AgentSoldApplianceService $agentSoldApplianceService)
    {
        $this->agentSoldApplianceService = $agentSoldApplianceService;
    }


    /**
     * List of Agent Sold Appliances for App
     * A list of the all sold appliances of the agent.
     * @responseFile responses/agent/agent.sold.appliances.json
     * @param  Agent   $agent
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $soldAppliances = $this->agentSoldApplianceService->list($agent->id);

        return new ApiResource($soldAppliances);
    }

    /**
     * Agent Customer Appliances
     * A list of the all sold appliance for the customer of authenticated agent.
     * @urlParam customerId required.
     * @responseFile responses/agent/agent.customer.sold.appliances.json
     * @param $customerId
     * @param Request $request
     * @return ApiResource
     */
    public function customerSoldAppliances($customerId, Request $request): ApiResource
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $soldAppliances = $this->agentSoldApplianceService->customerSoldList($customerId, $agent->id);

        return new ApiResource($soldAppliances);
    }

    /**
     * Create a Sold Appliance
     * Store a new sold appliance for the customer of authenticated agent.
     *
     * @bodyParam person_id int required
     * @bodyParam agent_assigned_appliance_id int required
     * @param  CreateAgentSoldApplianceRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentSoldApplianceRequest $request)
    {

        $appliance = $this->agentSoldApplianceService->create(
            $request->only(
                [
                'person_id',
                'agent_assigned_appliance_id',

                ]
            )
        );

        return new ApiResource($appliance);
    }

    /**
     * List of Agent Sold Appliances for Web
     * A list of the all sold appliances of the agent.
     * @responseFile responses/agent/agent.sold.appliances.json
     * @urlParam agentId required
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function indexWeb(Agent $agent, Request $request): ApiResource
    {

        $soldAppliances = $this->agentSoldApplianceService->listForWeb($agent->id);

        return new ApiResource($soldAppliances);
    }
}
