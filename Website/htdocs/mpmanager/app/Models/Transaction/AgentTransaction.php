<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;

/**
 * @property int agent_id
 * @property int device_id
 * @property int status
 * @property string sender
 */
class AgentTransaction extends BaseModel implements RawTransaction
{
    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'original_transaction');
    }

    public function conflicts()
    {
        return $this->morphMany(TransactionConflicts::class, 'transaction');
    }
}
