<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentBalanceHistory extends Model
{
    protected $guarded = [];

    public function agent()
    {
        $this->belongsTo(Agent::class);
    }

}
