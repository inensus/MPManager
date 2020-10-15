<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentSoldApplianceRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\AgentSoldAppliance;
use App\Services\AgentSoldApplianceService;
use Illuminate\Http\Request;

/**
 * @group Agent-Appliances
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
     * List
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
     * Create
     * Store a newly created resource in storage.
     * @bodyParam person_id int required
     * @bodyParam down_payment int required
     * @bodyParam tenure int required
     * @bodyParam first_payment_date date required
     * @bodyParam agent_assigned_appliance_id int required
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

    public function update(Request $request, agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    public function destroy(agentSoldAppliance $agent_sold_appliance)
    {
        //
    }

    /**
     * List for Web
     * @urlParam agent int required
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function indexWeb(Agent $agent, Request $request)
    {

        $soldAppliances = $this->agentSoldApplianceService->listForWeb($agent->id);

        return new ApiResource($soldAppliances);
    }
}
