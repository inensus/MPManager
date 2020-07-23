<?php

namespace App\Http\Controllers;


use App\Services\AgentService;
use App\Http\Requests\CreateAgentRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

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
        return new ApiResource($users);
    }


    public function store(CreateAgentRequest $request)
    {

        $agent = $this->agentService->create($request->only([
            'person_id',
            'name',
            'password',
            'email',
            'mini_grid_id'
        ]));

        return new ApiResource($agent);
    }

    public function update(Agent $agent, Request $request): ApiResource
    {
        $this->agentService->update($agent, $request->all());

        return new ApiResource($agent->fresh());
    }

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

    public function showBalance(Request $request, Response $response): Response
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $balance = $this->agentService->getAgentBalance($agent);
        return $response->setStatusCode(200)->setContent([
            'data' => [
                'balance' => $balance,
                'status_code' => 200
            ]
        ]);
    }
}
