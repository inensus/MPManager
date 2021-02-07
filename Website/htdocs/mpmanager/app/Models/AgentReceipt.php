<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\AgentBalanceHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentReceipt extends BaseModel
{
    public function history(): BelongsTo
    {
        return $this->belongsTo(AgentBalanceHistory::class, 'last_controlled_balance_history_id', 'id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(AgentReceiptDetail::class);
    }
}
