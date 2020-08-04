<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\Transaction\AgentTransaction;
use App\Services\AgentTransactionService;
use Illuminate\Http\Request;

class AgentTransactionsController extends Controller
{
    private $agentTransactionService;


    public function __construct(AgentTransactionService $agentTransactionService)
    {
        $this->agentTransactionService = $agentTransactionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $transactions = $this->agentTransactionService->list($agent->id);
        return new ApiResource($transactions);
    }

    public function agentCustomerTransactions($customerId, Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $transactions = $this->agentTransactionService->listByCustomer($agent->id, $customerId);
        return new ApiResource($transactions);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param AgentTransaction $agent_Transactions
     * @return void
     */
    public function update(Request $request, AgentTransaction $agent_Transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AgentTransaction $agent_Transactions
     * @return void
     */
    public function destroy(AgentTransaction $agent_Transactions)
    {
        //
    }

    public function indexWeb(Agent $agent)
    {
        $transactions = $this->agentTransactionService->listForWeb($agent->id);
        return new ApiResource($transactions);
    }
}
