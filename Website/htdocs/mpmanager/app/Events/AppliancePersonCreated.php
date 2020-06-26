<?php


namespace App\Events;


use App\Models\AppliancePerson;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppliancePersonCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var AppliancePerson
     */
    private $appliancePerson;

    public function __construct(AppliancePerson $appliancePerson)
    {
        $this->appliancePerson = $appliancePerson;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('appliancePerson.created');
    }
}
