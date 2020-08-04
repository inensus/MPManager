<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Services\AgentTransactionService;

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
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->list($agent->id);
        return new ApiResource($transactions);
    }

    public function show($customerId): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->listByCustomer($agent->id, $customerId);
        return new ApiResource($transactions);
    }
}
