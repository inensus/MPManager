<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentCommissionRequest;
use App\Http\Resources\ApiResource;
use App\Models\AgentCommission;
use App\Services\AgentCommissionService;
use Illuminate\Http\Request;

/**
 * @group Agent-Commissions
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
     * List
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): ApiResource
    {
        $commissions = AgentCommission::query()->paginate(config('settings.paginate'));
        return new ApiResource($commissions);
    }


    /**
     * Create
     * Create a new agent commission
     * @bodyParam name string required
     * @bodyParam energy_commission int required
     * @bodyParam appliance_commission int required
     * @bodyParam risk_balance int required
     * @param CreateAgentCommissionRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentCommissionRequest $request)
    {
        $commission = $this->agentCommissionService->create($request);
        return new ApiResource($commission);
    }


    /**
     * Update
     * Update the specified agent commission
     * @bodyParam name string required
     * @bodyParam energy_commission int required
     * @bodyParam appliance_commission int required
     * @bodyParam risk_balance int required
     * @param Request $request
     * @param AgentCommission $commission
     * @return void
     */
    public function update(CreateAgentCommissionRequest $request, AgentCommission $commission): ApiResource
    {
        $updatedAgentCommission = $this->agentCommissionService->update($commission, $request->all());
        return new ApiResource($updatedAgentCommission);
    }

    /**
     * Remove
     * Remove the specified agent commission
     * @bodyParam commission int required
     * @param AgentCommission $commission
     * @return void
     */
    public function destroy(AgentCommission $commission): ApiResource
    {
        return new ApiResource($this->agentCommissionService->delete($commission));
    }


}

