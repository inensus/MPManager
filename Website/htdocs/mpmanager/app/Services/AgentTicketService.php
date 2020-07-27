<?php


namespace App\Services;


use App\Models\Agent;
use App\Models\Person\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Inensus\Ticket\Models\Ticket;
use Inensus\Ticket\Services\BoardService;
use Inensus\Ticket\Services\CardService;
use Inensus\Ticket\Services\TicketService;
use Inensus\Ticket\Trello\Tickets;

class AgentTicketService implements IAgentRelatedService
{
    private $boardService;
    private $cardService;
    private $tickets;
    /**
     * @var TicketService
     */
    private $ticketsService;

    /**
     * AgentTicketService constructor.
     * @param BoardService $boardService
     * @param CardService $cardService
     */
    public function __construct(
        BoardService $boardService,
        CardService $cardService,
        TicketService $ticketsService,
        Tickets $tickets
    ) {
        $this->boardService = $boardService;
        $this->cardService = $cardService;
        $this->tickets = $tickets;
        $this->ticketsService = $ticketsService;
    }

    public function list($agentId)
    {
        $tickets = Ticket::query()->whereHasMorph('creator', [Agent::class],
            static function ($q) use ($agentId) {
                $q->where('id', $agentId);
            })
            ->latest()
            ->paginate(5);


        return $this->getTicketDetailsFromSource($tickets);

    }

    public function listByCustomer($agentId, $customerId)
    {

        return Ticket::query()->whereHasMorph('creator', [Agent::class],
            static function ($q) use ($agentId) {
                $q->where('id', $agentId);
            })
            ->where('owner_id', $customerId)
            ->latest()
            ->paginate();


    }


    protected function getTicketDetailsFromSource(LengthAwarePaginator $ticketList): LengthAwarePaginator
    {
        $ticket_ids = $ticketList->getCollection();
        if ($tickets->count()) {
            //get ticket details from trello
            $ticketData = $this->ticketsService->getBatch($ticket_ids);
            $ticketList->setCollection(Collection::make($ticketData));
        }

        return $ticketList;

    }

    public function create($ticketData)
    {
        $board = $this->boardService->initializeBoard();
        $card = $this->cardService->initalizeList($board);

        $ownerId = (int)$ticketData['owner_id'];
        try {
            $owner = Person::query()->findOrFail($ownerId);
        } catch (ModelNotFoundException $e) {
            throw new TicketOwnerNotFoundException("Owner (person) with following id not found " . $ownerId);
        }


        $creator = auth('agent_api')->user();


        //reformat due date if it is set
        $dueDate = $ticketData['due_date'] !== null ? date('Y-m-d H:i:00', strtotime($ticketData['due_date'])) : null;
        $categoryId = $ticketData['label'];


        $trelloParams = [
            'idList' => $card->card_id,
            'name' => $ticketData['title'],
            'desc' => $ticketData['description'],
            'due' => $dueDate === '1970-01-01' ? null : $dueDate,
        ];

        $ticketId = $this->tickets->createTicket($trelloParams)->id;

        $ticket = Ticket::make([
            'ticket_id' => $ticketId,
            'category_id' => $categoryId,

        ]);
        $ticket->creator()->associate($creator);
        $ticket->owner()->associate($owner);
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
