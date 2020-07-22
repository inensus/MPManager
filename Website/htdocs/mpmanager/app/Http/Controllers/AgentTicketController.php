<?php

namespace App\Http\Controllers;

use App\AgentTicket;
use App\Http\Requests\CreateAgentTicketRequest;
use App\Http\Resources\ApiResource;
use App\Models\Agent;
use App\Services\AgentTicketService;
use Illuminate\Http\Request;
use Inensus\Ticket\Http\Resources\TicketResource;


class AgentTicketController extends Controller
{
    private $agentTicketService;


    public function __construct(
        AgentTicketService $agentTicketService
    ) {

        $this->agentTicketService = $agentTicketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Agent $agent
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $tickets = $this->agentTicketService->list($agent->id);

        return new ApiResource($tickets);
    }

    public function agentCustomerTickets($customerId, Request $request)
    {
        $agent = Agent::find(auth('agent_api')->user()->id);
        $tickets = $this->agentTicketService->listByCustomer($agent->id, $customerId);
        return new ApiResource($tickets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentTicketRequest $request)
    {


        $ticket = $this->agentTicketService->create($request->only([
            'owner_id',
            'assignedPerson',
            'dueDate',
            'label',
            'title',
            'description'
        ]));

        return new TicketResource($this->agentTicketService->getBatch([$ticket]));

    }

    /**
     * Display the specified resource.
     *
     * @param \App\AgentTicket $agentTicket
     * @return \Illuminate\Http\Response
     */
    public function show(AgentTicket $agentTicket)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AgentTicket $agentTicket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgentTicket $agentTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\AgentTicket $agentTicket
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgentTicket $agentTicket)
    {
        //
    }
}
