<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAgentTicketRequest;
use App\Http\Resources\ApiResource;
use App\Services\AgentTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inensus\Ticket\Exceptions\TicketOwnerNotFoundException;
use Inensus\Ticket\Http\Resources\TicketResource;

/**
 * @group   Agent Ticket
 * Class AgentTicketController
 * @package App\Http\Controllers
 */

class AgentTicketController extends Controller
{
    private $agentTicketService;


    public function __construct(AgentTicketService $agentTicketService)
    {

        $this->agentTicketService = $agentTicketService;
    }

    /**
     * List of all Agent Tickets
     * A list of the all ticket for the given agent.
     * @responseFile responses/agent/agent.tickets.json
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->list($agent->id);

        return new ApiResource($tickets);
    }

    /**
     * List of Tickets by Customer
     * A list of all tickets for the customer of authenticated agent.
     * @urlParam customerId required
     * @param $customerId
     * @param Request $request
     * @return ApiResource
     */
    public function agentCustomerTickets($customerId, Request $request): ApiResource
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->listByCustomer($agent->id, $customerId);
        return new ApiResource($tickets);
    }

    /**
     * Create a new Ticket
     * Create a new ticket for the customer of agent.
     *
     * @bodyParam owner_id int. required
     * @bodyParam due_date date. required
     * @bodyParam label string. required
     * @bodyParam title string. required
     * @bodyParam description string. required
     * @param  CreateAgentTicketRequest $request
     * @return JsonResponse|TicketResource
     */
    public function store(CreateAgentTicketRequest $request)
    {
        try {
            $ticket = $this->agentTicketService->create(
                $request->only(
                    [
                    'owner_id',
                    'due_date',
                    'label',
                    'title',
                    'description'
                    ]
                )
            );
        } catch (TicketOwnerNotFoundException $e) {
            return response()->setStatusCode(409)->json(['success' => 0, 'message' => $e->getMessage()]);
        }
        return new TicketResource($this->agentTicketService->getBatch([$ticket]));
    }

    /**
     * Detail of Agent Ticket
     * Detail of ticket for the given ticketId.
     * @responseFile responses/agent/agent.ticket.detail.json
     * @urlParam ticketId required
     * @param $ticketId
     * @return ApiResource
     */
    public function show($ticketId): ApiResource
    {
        $ticket = $this->agentTicketService->getTicket($ticketId);
        return new ApiResource($ticket);
    }
}
