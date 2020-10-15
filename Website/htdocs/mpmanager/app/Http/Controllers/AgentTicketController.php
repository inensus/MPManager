<?php

namespace App\Http\Controllers;

use App\AgentTicket;
use App\Http\Requests\CreateAgentTicketRequest;
use App\Http\Resources\ApiResource;
use App\Services\AgentTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inensus\Ticket\Exceptions\TicketOwnerNotFoundException;
use Inensus\Ticket\Http\Resources\TicketResource;

/**
 * @group Agent-Tickets
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
     * List
     * List of all agent tickets
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->list($agent->id);

        return new ApiResource($tickets);
    }

    /**
     * List of Agent Customer Tickets
     * @urlParam customerId int required
     * @param $customerId
     * @param Request $request
     * @return ApiResource
     */

    public function agentCustomerTickets($customerId, Request $request)
    {
        $agent = request()->attributes->get('user');
        $tickets = $this->agentTicketService->listByCustomer($agent->id, $customerId);
        return new ApiResource($tickets);
    }

    /**
     * Create
     * Create a new ticket
     * @bodyParam owner_id int required
     * @bodyParam due_date date required
     * @bodyParam label string required
     * @bodyParam title string required
     * @bodyParam  description string required
     * @param CreateAgentTicketRequest $request
     * @return JsonResponse|TicketResource
     */
    public function store(CreateAgentTicketRequest $request)
    {
        try {
            $ticket = $this->agentTicketService->create($request->only([
                'owner_id',
                'due_date',
                'label',
                'title',
                'description'
            ]));
        } catch (TicketOwnerNotFoundException $e) {
            return response()->setStatusCode(409)->json(['success' => 0, 'message' => $e->getMessage()]);
        }
        return new TicketResource($this->agentTicketService->getBatch([$ticket]));
    }

    /**
     * Detail
     * Detail of the specified agent ticket
     * @urlParam ticketId int required
     * @param $ticketId
     * @return ApiResource
     */
    public function show($ticketId): ApiResource
    {
        $ticket = $this->agentTicketService->getTicket($ticketId);
        return new ApiResource($ticket);
    }
}
