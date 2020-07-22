<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class agentBalanceHistory extends Model
{
    public function agent()
    {
        $this->belongsTo(Agent::class);
    }
    public function  user(){
        $this->belongsTo(User::class);
    }
}
