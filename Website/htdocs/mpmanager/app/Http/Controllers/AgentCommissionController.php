<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentCommissionRequest;
use App\Http\Resources\ApiResource;
use App\Models\AgentCommission;
use App\Services\AgentCommissionService;
use Illuminate\Http\Request;

/**
 * @group   Agent Commission
 * Class AgentCommissionController
 * @package App\Http\Controllers
 */

class AgentCommissionController extends Controller
{

    private $agentCommissionService;

    public function __construct(AgentCommissionService $agentCommissionService)
    {
        $this->agentCommissionService = $agentCommissionService;
    }

    /**
     * List Agent Commissions
     * Display a listing of the all commissions.
     * @responseFile responses/agent/agent.commissions.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $commissions = AgentCommission::query()->paginate(config('settings.paginate'));
        return new ApiResource($commissions);
    }


    /**
     * Create commission
     * Create a new agent commission.
     * @bodyParam name string. required
     * @bodyParam energy_commission int. required
     * @bodyParam appliance_commission int. required
     * @bodyParam risk_balance int. required
     *
     * @param  CreateAgentCommissionRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentCommissionRequest $request)
    {
        $commission = $this->agentCommissionService->create();
        return new ApiResource($commission);
    }


    /**
     * Update commission
     * Update agent commission for the given id.
     * @urlParam AgentCommissionId int required
     *
     * @bodyParam name string. required
     * @bodyParam energy_commission int. required
     * @bodyParam appliance_commission int. required
     * @bodyParam risk_balance int. required
     * @param CreateAgentCommissionRequest $request
     * @param AgentCommission $commission
     *
     * @return ApiResource
     */
    public function update(CreateAgentCommissionRequest $request, AgentCommission $commission): ApiResource
    {
        $updatedAgentCommission = $this->agentCommissionService->update($commission, $request->all());
        return new ApiResource($updatedAgentCommission);
    }

    /**
     * Delete commission
     * Remove agent commission for the given id.
     * @urlParam AgentCommissionId int required
     * @param AgentCommission $commission
     *
     * @return ApiResource
     * @throws \Exception
     */
    public function destroy(AgentCommission $commission): ApiResource
    {
        return new ApiResource($this->agentCommissionService->delete($commission));
    }
}
