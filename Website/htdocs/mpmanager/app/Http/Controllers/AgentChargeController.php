<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentChargeRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentChargeService;
use Illuminate\Http\Request;

/**
 * @group   Agent Charge
 * Class AgentChargeController
 * @package App\Http\Controllers
 */

class AgentChargeController extends Controller
{
    private $agentChargeService;

    public function __construct(AgentChargeService $agentChargeService)
    {
        $this->agentChargeService = $agentChargeService;
    }

    /**
     * Charge Agent Balance
     * It charge agent balance.
     * @urlParam agentId required The ID of agent
     *
     * @bodyParam agent_id int. required
     * @bodyParam user_id int. required
     * @bodyParam amount float. required
     * @bodyParam available_balance float. required
     * @bodyParam due_to_supplier float. required
     *
     * @param     Agent               $agent
     * @param     CreateAgentChargeRequest $request
     * @return    ApiResource
     */
    public function store(Agent $agent, CreateAgentChargeRequest $request): ApiResource
    {
        $agentCharge = $this->agentChargeService->create($agent, $request->only(['user_id']));
        return new ApiResource($agentCharge);
    }
}
