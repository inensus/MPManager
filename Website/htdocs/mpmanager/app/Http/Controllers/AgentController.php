<?php

namespace App\Http\Controllers;


use App\Services\AgentService;
use App\Http\Requests\CreateAgentRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * @group Agents
 * Class AgentController
 * @package App\Http\Controllers
 */
class AgentController extends Controller
{
    private $agentService;

    public function __construct(AgentService $agentService)
    {

        $this->agentService = $agentService;
    }

    /**
     * List
     * List of All Agents
     * @param Request $request
     * @return ApiResource
     */

    public function index(Request $request): ApiResource
    {
        $users = $this->agentService->list($request->all());
        return new ApiResource($users);
    }

    /**
     * Details
     * Agent Detail with following Relations
     * - person
     * - person.addresses
     * - miniGrid
     * - commission
     * @urlParam agentId int required
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function show(Agent $agent, Request $request): ApiResource
    {

        $agent = $this->agentService->getAgentDetail($agent);
        return new ApiResource($agent);
    }

    /**
     * Create
     * Create a new Agent
     * @bodyParam email string required
     * @bodyParam name string required
     * @bodyParam surname string required
     * @bodyParam password string required
     * @bodyParam city_id int required
     * @bodyParam agent_commission_id int required
     * @param CreateAgentRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentRequest $request)
    {
        $agent = $this->agentService->createFromRequest($request);


        return new ApiResource($agent);
    }

    /**
     * Update
     * Update the specified Agent
     * @bodyParam name string
     * @bodyParam surname string
     * @bodyParam gender string
     * @bodyParam birthday date
     * @bodyParam phone int
     * @bodyParam commissionTypeId int
     * @bodyParam address string
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function update(Agent $agent, Request $request): ApiResource
    {

        $updatedAgent = $this->agentService->update($agent, $request->all());

        return new ApiResource($updatedAgent);
    }

    /**
     * Remove
     * Remove the specified agent.
     * @bodyParam agent int required
     * @param Agent $agent
     * @return ApiResource
     */
    public function destroy(Agent $agent): ApiResource
    {

        return new ApiResource($this->agentService->deleteAgent($agent));
    }

    /**
     * Search
     * @urlParam term string required
     * @urlParam paginate int
     * @return ApiResource
     */
    public function search()
    {

        $term = request('term');
        $paginate = request('paginate') ?? 1;

        return new ApiResource($this->agentService->searchAgent($term, $paginate));
    }

    /**
     * Reset Password
     * @bodyParam email required
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function resetPassword(Request $request, Response $response): Response
    {
        $responseMessage = $this->agentService->resetPassword($request->input('email'));

        if ($responseMessage == 'Invalid email.') {
            return $response->setStatusCode(422)->setContent([
                'data' => [
                    'message' => $responseMessage,
                    'status_code' => 400
                ]
            ]);
        }

        return $response->setStatusCode(200)->setContent([
            'data' => [
                'message' => $responseMessage,
                'status_code' => 200
            ]
        ]);
    }

    public function setFirebaseToken(Request $request): ApiResource
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $this->agentService->setFirebaseToken($agent, $request->input('fire_base_token'));

        return new ApiResource($agent->fresh());
    }


    public function showDashboardBoxes(Request $request, Response $response): Response
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $average = $this->agentService->getTransactionAverage($agent);
        $since = $this->agentService->getLastReceiptDate($agent);
        return $response->setStatusCode(200)->setContent([
            'data' => [
                'balance' => $agent->balance,
                'profit' => $agent->commission_revenue,
                'dept' => $agent->due_to_energy_supplier,
                'average' => $average,
                'since' => $since,
                'status_code' => 200
            ]
        ]);
    }

    /**
     * Balance History
     *
     * @param Request $request
     * @param Response $response
     * @return array|false|string
     */
    public function showBalanceHistories(Request $request, Response $response)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $graphValues = $this->agentService->getGraphValues($agent);
        return $graphValues;

    }

    /**
     * Weekly Revenues
     * @param Request $request
     * @param Response $response
     * @return array
     */
    public function showRevenuesWeekly(Request $request, Response $response)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        return $this->agentService->getAgentRevenuesWeekly($agent);

    }
}
