<?php


namespace App\Services;


use App\Models\Agent;
use Inensus\Ticket\Models\Ticket;
use Inensus\Ticket\Services\BoardService;
use Inensus\Ticket\Services\CardService;
use Inensus\Ticket\Services\UserService;
use Inensus\Ticket\Trello\Tickets;

class AgentTicketService implements IAgentRelatedService
{
    private $boardService;
    private $cardService;
    private $userService;
    private $tickets;
    /**
     * AgentTicketService constructor.
     * @param BoardService $boardService
     * @param CardService $cardService
     * @param UserService $userService
     */
    public function __construct(
        BoardService $boardService,
        CardService $cardService,
        UserService $userService,
        Tickets $tickets
    ) {
        $this->boardService = $boardService;
        $this->cardService = $cardService;
        $this->userService = $userService;
        $this->tickets = $tickets;
    }

    public function list($agentId)
    {
        return Ticket::with(['agent',])
            ->where('creator_type', 'agent')
            ->whereHas('agent', function ($q) use ($agentId) {
                $q->where('id', $agentId);
            })
            ->latest()->paginate();
    }

    public function listByCustomer($agentId, $customerId)
    {
        return Ticket::with(['agent',])
            ->where('owner_id', $customerId)
            ->where('creator_type', 'agent')
            ->whereHas('agent', function ($q) use ($agentId) {
                $q->where('id', $agentId);
            })
            ->latest()->paginate();
    }
    public function create($ticketData)
    {
        $board = $this->boardService->initializeBoard();
        $card = $this->cardService->initalizeList($board);

        $ownerId =  (int)$ticketData['owner_id'];
        $ownerType = 'person';
        $assignedId = $ticketData['assignedPerson'];



        $creatorId = Agent::find(auth('agent_api')->user()->id)->id;
        $creatorType = 'agent';

        //reformat due date if it is set
        $dueDate =  $ticketData['dueDate']!== null ? date('Y-m-d H:i:00', strtotime($ticketData['dueDate'])) : null;
        $category = $ticketData['label'];


        $trelloParams = [
            'idList' => $card->card_id,
            'name' =>  $ticketData['title'],
            'desc' =>  $ticketData['description'],
            'due' => $dueDate === '1970-01-01' ? null : $dueDate,
            'idMembers' => $assignedId,
        ];
        $assignedUser = $assignedId ? $this->userService->getByExternId($assignedId)->id : null;
        $ticketId = $this->tickets->createTicket($trelloParams)->id;
        $ticket = new Ticket();
        $ticket->ticket_id = $ticketId;
        $ticket->owner_type = $ownerType;
        $ticket->owner_id = $ownerId;
        $ticket->creator_id = $creatorId;
        $ticket->creator_type =$creatorType;
        $ticket->category_id = $category;
        $ticket->assigned_id = $assignedId;
        $ticket->save();
        return $ticket;

    }

    public function getBatch($tickets)
    {
        $ticketData = [];

        foreach ($tickets as $index => $ticket) {

            $tickets[$index]['ticket'] = $this->getTicket($ticket->ticket_id);
            $tickets[$index]['actions'] = $this->getActions($ticket->ticket_id);
            //$t['self'] = $ticket;


        }

        return $tickets;

    }
    public function getTicket($ticketId)
    {
        return $this->tickets->get($ticketId);
    }

    public function getActions($ticketId)
    {
        return $this->tickets->actions($ticketId);
    }
}
