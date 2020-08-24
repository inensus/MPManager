<?php

namespace App\Models;

use App\Relations\BelongsToMorph;
use Illuminate\Database\Eloquent\Model;
use Inensus\Ticket\Models\Ticket;

class AgentTicket extends Model
{
    protected $guarded = [];

    public function ticket()
    {
        return $this->morphOne(Ticket::class, 'original_');
    }

    //
}
