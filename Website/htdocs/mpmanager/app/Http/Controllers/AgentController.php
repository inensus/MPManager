<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AgentController extends Controller
{
    private $agentService;

    public function __construct(AgentService $agentService)
    {

        $this->agentService = $agentService;
    }

    public function index(Request $request): ApiResource
    {
        $users = $this->agentService->list($request->all());
       return ApiResource::make($users);
    }

    public function show(Agent $agent, Request $request): ApiResource
    {
        $agent = $this->agentService->getAgentDetail($agent);
       return ApiResource::make($agent);
    }

    public function store(CreateAgentRequest $request): ApiResource
    {
        $agent = $this->agentService->createFromRequest($request);
       return ApiResource::make($agent);
    }

    public function update(Agent $agent, Request $request): ApiResource
    {
        $updatedAgent = $this->agentService->update($agent, $request->all());
       return ApiResource::make($updatedAgent);
    }

    public function destroy(Agent $agent): ApiResource
    {
       return ApiResource::make($this->agentService->deleteAgent($agent));
    }


    public function search(): ApiResource
    {
        $term = request('term');
        $paginate = request('paginate') ?? 1;

       return ApiResource::make($this->agentService->searchAgent($term, $paginate));
    }

    /**
     * @return Response
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

    public function setFirebaseToken(Request $request): ApiResource
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $this->agentService->setFirebaseToken($agent, $request->input('fire_base_token'));

       return ApiResource::make($agent->fresh());
    }


    /**
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

    public function showBalanceHistories(Request $request, Response $response)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $graphValues = $this->agentService->getGraphValues($agent);
        return $graphValues;
    }

    public function showRevenuesWeekly(Request $request, Response $response)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        return $this->agentService->getAgentRevenuesWeekly($agent);
    }
}
