<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentCustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   Agent Customer
 * Class AgentCustomerController
 * @package App\Http\Controllers
 */

class AgentCustomerController extends Controller
{

    private $agentCustomerService;

    public function __construct(AgentCustomerService $agentCustomerService)
    {
        $this->agentCustomerService = $agentCustomerService;
    }

    /**
     * List of Agent Customers
     * A List of the customers of authenticated agent.
     *
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        return new ApiResource($this->agentCustomerService->list($agent));
    }

    /**
     * Search from Agent Customer
     * Search customer of agent for the given terms.
     * @urlParam term required
     * @return ApiResource
     */
    public function search(): ApiResource
    {
        $term = request('term');
        $paginate = request('paginate') ?? 1;
        $agent = Agent::find(auth('agent_api')->user()->id);
        return new ApiResource($this->agentCustomerService->searchAgentsCustomers($term, $paginate, $agent));
    }
}
