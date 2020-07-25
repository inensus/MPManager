<?php

namespace App\Models;

use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Model;

class AgentBalanceHistory extends Model
{
    protected $guarded = [];

    public function agent()
    {
        $this->belongsTo(Agent::class);
    }


    public function trigger()
    {
        return $this->morphTo();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
