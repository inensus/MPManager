<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentCommissionRequest;
use App\Http\Resources\ApiResource;
use App\Models\AgentCommission;
use App\Services\AgentCommissionService;
use Illuminate\Http\Request;

class AgentCommissionController extends Controller
{

    private $agentCommissionService;

    public function __construct(AgentCommissionService $agentCommissionService)
    {
        $this->agentCommissionService = $agentCommissionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $commissions = AgentCommission::query()->paginate(config('settings.paginate'));
       return ApiResource::make($commissions);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateAgentCommissionRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentCommissionRequest $request)
    {
        $commission = $this->agentCommissionService->create();
       return ApiResource::make($commission);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param CreateAgentCommissionRequest $request
     * @param AgentCommission $commission
     *
     * @return ApiResource
     */
    public function update(CreateAgentCommissionRequest $request, AgentCommission $commission): ApiResource
    {
        $updatedAgentCommission = $this->agentCommissionService->update($commission, $request->all());
       return ApiResource::make($updatedAgentCommission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AgentCommission $commission
     *
     * @return ApiResource
     * @throws \Exception
     */
    public function destroy(AgentCommission $commission): ApiResource
    {
       return ApiResource::make($this->agentCommissionService->delete($commission));
    }
}
