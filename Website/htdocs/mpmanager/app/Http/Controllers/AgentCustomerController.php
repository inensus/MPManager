<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentCustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Agent-Customers
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
     * List
     *List of all agents customers
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
       return new ApiResource($this->agentCustomerService->list($agent));
    }

    /**
     * Search
     * @urlParam term string required
     * @urlParam paginate int required
     * @urlParam agent_api required
     * @return ApiResource
     */
    public function search()
    {
        $term = request('term');
        $paginate = request('paginate') ?? 1;
        $agent = Agent::find(auth('agent_api')->user()->id);
        return new ApiResource($this->agentCustomerService->searchAgentsCustomers($term, $paginate,$agent));


    }
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
