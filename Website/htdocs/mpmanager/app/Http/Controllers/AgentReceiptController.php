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
 * @group   Agent Receipt
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
     * List of Agent Receipts
     * A list of the all receipts of the agent.
     * @urlParam agentId required
     * @responseFile responses/agent/agent.receipts.json
     * @param  Agent   $agent
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Agent $agent, Request $request)
    {

        $agentReceipts = $this->agentReceiptService->list($agent->id);
        return new ApiResource($agentReceipts);
    }

    /**
     * List of Agent Receipts By User
     * A list of the receipts of for the given user.
     *
     * @param  User    $user
     * @param  Request $request
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
     * A list of the all receipts.
     *
     * @param  Request $request
     * @return ApiResource
     */
    public function listAllReceipts(Request $request)
    {

        $agentReceipts = $this->agentReceiptService->listAllReceipts();
        return new ApiResource($agentReceipts);
    }

    /**
     * Create a new agent receipt
     *
     * Store a newly created resource in storage.
     *
     * @bodyParam amount float required
     * @param  Agent                     $agent
     * @param  CreateAgentReceiptRequest $request
     * @return ApiResource
     */
    public function store(Agent $agent, CreateAgentReceiptRequest $request)
    {

        $user = User::find(auth('api')->user()->id);
        $appliance = $this->agentReceiptService->create(
            $user->id,
            $agent->id,
            $request->only(
                [
                'amount',
                ]
            )
        );

        return new ApiResource($appliance);
    }


    /**
     * Update Agent Receipt
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Delete
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @return void
     */
    public function destroy(Request $request)
    {
        //
    }
}
