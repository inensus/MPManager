<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Models\Transaction\AgentTransaction;
use App\Services\AgentTransactionService;
use Illuminate\Http\Request;

/**
 * @group Agent-Transactions
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
     * List
     * List of all agents transactions
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->list($agent->id);
        return new ApiResource($transactions);
    }

    /**
     * List Customer Transactions
     * List of all the specified agent customer transactions
     * @urlParam customerId int required
     * @param $customerId
     * @param Request $request
     * @return ApiResource
     */
    public function agentCustomerTransactions($customerId, Request $request)
    {
        $agent = request()->attributes->get('user');
        $transactions = $this->agentTransactionService->listByCustomer($agent->id, $customerId);
        return new ApiResource($transactions);
    }

    /**
     * Detail
     * Detail of the specified Agent Customer Transactions
     * @urlParam customerId int required
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
     * List for Web
     * @urlParam agentId int Required
     * @param Agent $agent
     * @return ApiResource
     */
    public function indexWeb(Agent $agent)
    {
        $transactions = $this->agentTransactionService->listForWeb($agent->id);
        return new ApiResource($transactions);
    }
}
