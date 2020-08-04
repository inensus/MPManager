<?php

namespace App\Models;

use App\Models\Person\Person;
use Illuminate\Database\Eloquent\Model;

class AgentCharge extends BaseModel
{
    //

    public function agent()
    {
        $this->belongsTo(Agent::class);
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function history()
    {
        return $this->morphOne(AgentBalanceHistory::class, 'trigger');
    }
}
