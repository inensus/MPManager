<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   Agent
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
     * A list of all agents.
     * @responseFile responses/agent/agents.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $users = $this->agentService->list();
        return new ApiResource($users);
    }

    /**
     * Detail
     * Detail of agent for the given id.
     * @responseFile responses/agent/agent.detail.json
     * @urlParam agentId required
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function show(Agent $agent, Request $request): ApiResource
    {
        $agent = $this->agentService->get($agent->id);
        return new ApiResource($agent);
    }

    /**
     * Create
     * Create a new agent.
     *
     * @bodyParam title string.
     * @bodyParam education string.
     * @bodyParam name string. required
     * @bodyParam surname string. required
     * @bodyParam birth_date string.
     * @bodyParam sex string.
     * @bodyParam is_customer bool. required
     * @param CreateAgentRequest $request
     * @return ApiResource
     */
    public function store(CreateAgentRequest $request): ApiResource
    {
        $agent = $this->agentService->createFromRequest($request);
        return new ApiResource($agent);
    }

    /**
     * Update
     * Update agent for the given id.
     * @urlParam agentId int. required
     *
     * @bodyParam name string
     * @bodyParam surname string
     * @bodyParam gender bool
     * @bodyParam birthday date
     * @bodyParam phone string
     * @bodyParam commissionTypeId int
     *
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
     * Delete a agent for the given id.
     * @urlParam agentId required
     *
     * @param Agent $agent
     * @return ApiResource
     */
    public function destroy(Agent $agent): ApiResource
    {
        return new ApiResource($this->agentService->deleteAgent($agent));
    }

    /**
     * Search
     * Search agent for the given terms.
     * @urlParam term required
     * @return ApiResource
     */
    public function search(): ApiResource
    {
        $term = request('term');
        $paginate = request('paginate') ?? 1;

        return new ApiResource($this->agentService->searchAgent($term, $paginate));
    }

    /**
     * Reset Pass
     * Reset agent password for the given agent email.
     * @urlParam email required
     * @param Request $request
     * @param Response $response
     * @return $this
     */
    public function resetPassword(Request $request, Response $response): self
    {
        $responseMessage = $this->agentService->resetPassword($request->input('email'));

        if ($responseMessage === 'Invalid email.') {
            return $response->setStatusCode(422)->setContent(
                [
                    'data' => [
                        'message' => $responseMessage,
                        'status_code' => 400
                    ]
                ]
            );
        }

        return $response->setStatusCode(200)->setContent(
            [
                'data' => [
                    'message' => $responseMessage,
                    'status_code' => 200
                ]
            ]
        );
    }

    /**
     * Set FireBase Token
     *
     *
     * @bodyParam fire_base_token string. required
     * @param Request $request
     * @return ApiResource
     */
    public function setFirebaseToken(Request $request): ApiResource
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $this->agentService->setFirebaseToken($agent, $request->input('fire_base_token'));

        return new ApiResource($agent->fresh());
    }


    /**
     * Detail for Dashboard
     *
     * @param Request $request
     * @param Response $response
     * @return AgentController|Response|object
     */
    public function showDashboardBoxes(Request $request, Response $response)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $average = $this->agentService->getTransactionAverage($agent);
        $since = $this->agentService->getLastReceiptDate($agent);
        return $response->setStatusCode(200)->setContent(
            [
                'data' => [
                    'balance' => $agent->balance,
                    'profit' => $agent->commission_revenue,
                    'dept' => $agent->due_to_energy_supplier,
                    'average' => $average,
                    'since' => $since,
                    'status_code' => 200
                ]
            ]
        );
    }

    /**
     * Show Balance History
     *
     * @param Request $request
     * @param Response $response
     * @return array|false|\int[][]|string
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
