<?php

namespace App\Models;

use App\Models\Transaction\Transaction;
use App\Relations\BelongsToMorph;
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
    public function triggerCharge()
    {
        return BelongsToMorph::build($this, AgentCharge::class, 'trigger');
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
