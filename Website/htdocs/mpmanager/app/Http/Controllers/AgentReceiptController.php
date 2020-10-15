<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentAssignedApplianceRequest;
use App\Http\Requests\CreateAgentReceiptRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\User;
use App\Services\AgentReceiptService;
use Illuminate\Http\Request;

/**
 * @group Agent-Receipts
 * Class AgentReceiptController
 * @package App\Http\Controllers
 */
class AgentReceiptController extends Controller
{

    private $agentReceiptService;

    public function __construct(AgentReceiptService $agentReceiptService)
    {
        $this->agentReceiptService = $agentReceiptService;
    }

    /**
     * List Agent Receipts
     * List of the specified Agent Receipts.
     * @urlParam agentId int required
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function index(Agent $agent, Request $request)
    {

        $agentReceipts = $this->agentReceiptService->list($agent->id);
        return new ApiResource($agentReceipts);
    }

    /**
     * Detail
     * @urlParam userId int required
     * @param User $user
     * @param Request $request
     * @return ApiResource
     */
    public function listByUsers(Request $request)
    {
        $user = User::find(auth('api')->user()->id);
        $agentReceipts = $this->agentReceiptService->listReceiptsForUser($user->id);
        return new ApiResource($agentReceipts);
    }

    /**
     * List All Receipts
     *
     * @param Request $request
     * @return ApiResource
     */
    public function listAllReceipts(Request $request)
    {

        $agentReceipts = $this->agentReceiptService->listAllReceipts();
        return new ApiResource($agentReceipts);
    }

    /**
     * Create
     * Create a new Agent Receipt
     * @bodyParam amount int required
     * @param Agent $agent
     * @param CreateAgentReceiptRequest $request
     * @return ApiResource
     */
    public function store(Agent $agent,CreateAgentReceiptRequest $request)
    {

        $user = User::find(auth('api')->user()->id);
        $appliance = $this->agentReceiptService->create($user->id, $agent->id,$request->only([
            'amount',
        ]));

        return new ApiResource($appliance);
    }

    public function update(Request $request)
    {
        //
    }

    public function destroy(Request $request)
    {
        //
    }
}
