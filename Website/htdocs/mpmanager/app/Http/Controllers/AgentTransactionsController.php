<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentTransactionService;
use Illuminate\Http\Request;

/**
 * @group   Agent Transactions
 * Class AgentTransactionController
 * @package App\Http\Controllers
 */
class AgentTransactionsController extends Controller
{
    private $agentTransactionService;


    public function __construct(AgentTransactionService $agentTransactionService)
    {
        $this->agentTransactionService = $agentTransactionService;
    }

    /**
     * List Agent Transactions for app
     * A list of the all transactions.
     * @responseFile responses/agent/agent.transactions.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->list($agent->id);
        return new ApiResource($transactions);
    }

    /**
     * List of Transaction by Customer
     * @responseFile responses/agent/agent.transaction.detail.json
     * @urlParam customerId required
     * @param $customerId
     * @param Request $request
     * @return ApiResource
     */
    public function agentCustomerTransactions($customerId, Request $request): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->listByCustomer($agent->id, $customerId);
        return new ApiResource($transactions);
    }

    /**
     * Detail agent transaction
     * Detail of transaction for the given customerId.
     * @responseFile responses/agent/agent.transaction.detail.json
     * @urlParam customerId required.
     * @param $customerId
     * @return ApiResource
     */
    public function show($customerId): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->listByCustomer($agent->id, $customerId);
        return new ApiResource($transactions);
    }

    /**
     * List Agent Transactions for web
     * A list of the all transactions for the given agent.
     * @responseFile responses/agent/agent.transactions.json
     * @urlParam agentId required
     * @param Agent $agent
     * @return ApiResource
     */
    public function indexWeb(Agent $agent): ApiResource
    {
        $transactions = $this->agentTransactionService->listForWeb($agent->id);
        return new ApiResource($transactions);
    }
}
