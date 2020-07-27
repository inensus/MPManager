<?php

namespace App\Http\Controllers;

use App\AgentTicket;
use App\Http\Requests\CreateAgentTicketRequest;
use App\Http\Resources\ApiResource;
use App\Services\AgentTicketService;
use Illuminate\Http\Request;
use Inensus\Ticket\Exceptions\TicketOwenerNotFoundException;
use Inensus\Ticket\Http\Resources\TicketResource;


class AgentTicketController extends Controller
{
    private $agentTicketService;


    public function __construct(AgentTicketService $agentTicketService)
    {

        $this->agentTicketService = $agentTicketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->list($agent->id);

        return new ApiResource($tickets);
    }

    public function agentCustomerTickets($customerId, Request $request)
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->listByCustomer($agent->id, $customerId);
        return new ApiResource($tickets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentTicketRequest $request): TicketResource
    {
        try {
            $ticket = $this->agentTicketService->create($request->only([
                'owner_id',
                'due_date',
                'label',
                'title',
                'description'
            ]));
        } catch (TicketOwenerNotFoundException $e) {
            return response()->setStatusCode(409)->json(['success' => 0, 'message' => $e->getMessage()]);
        }

        return new TicketResource($this->agentTicketService->getBatch([$ticket]));

    }
}
