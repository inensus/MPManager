<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentCustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AgentCustomerController extends Controller
{

    private $agentCustomerService;

    public function __construct(AgentCustomerService $agentCustomerService)
    {

        $this->agentCustomerService = $agentCustomerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
       return new ApiResource($this->agentCustomerService->list($agent));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
