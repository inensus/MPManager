<?php

namespace App\Events;

use App\Models\Cluster;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class ClusterEvent
{
	use Dispatchable, InteractsWithSockets, SerializesModels;
	/**
	 * @var Cluster
	 */
	public $cluster;
	/**
	 * @var string
	 */
	public $type;
	/**
	 * @var mixed contains geo coordinates array or external url to fetch
	 */
	public $data;


	/**
	 * Create a new event instance.
	 *
	 * @param Cluster $cluster
	 * @param string $type
	 * @param $data
	 */
	public function __construct(Cluster $cluster, string $type, $data)
	{
		Log::debug('cluster event created');
		$this->cluster = $cluster;
		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('clusters');
	}
}
