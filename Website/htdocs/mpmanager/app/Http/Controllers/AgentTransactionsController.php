<?php

namespace App\Http\Controllers;

use App\Agent_Transactions;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\Transaction\Transaction;
use App\Services\AgentService;
use App\Services\AgentTransactionService;
use Illuminate\Http\Request;

class AgentTransactionsController extends Controller
{
    private $agentTransactionService;


    public function __construct(AgentTransactionService $agentTransactionService)
    {
        $this->agentTransactionService = $agentTransactionService;
        $this->middleware('auth:agent_api', ['except' => ['login']]);

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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Agent_Transactions $agent_Transactions
     * @return \Illuminate\Http\Response
     */
    public function show(Agent_Transactions $agent_Transactions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Agent_Transactions $agent_Transactions
     * @return \Illuminate\Http\Response
     */
    public function edit(Agent_Transactions $agent_Transactions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Agent_Transactions $agent_Transactions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agent_Transactions $agent_Transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Agent_Transactions $agent_Transactions
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent_Transactions $agent_Transactions)
    {
        //
    }
}
