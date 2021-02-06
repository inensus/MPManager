<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\AgentBalanceHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AgentReceipt extends BaseModel
{
    public function history()
    {
        return $this->belongsTo(AgentBalanceHistory::class, 'last_controlled_balance_history_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(AgentReceiptDetail::class);
    }
}
