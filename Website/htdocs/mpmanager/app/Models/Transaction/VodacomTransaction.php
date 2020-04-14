<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;


/**
 * @property mixed conversation_id
 * @property mixed originator_conversation_id
 * @property mixed id
 * @property mixed transaction_id
 * @property string mpesa_receipt
 * @property string transaction_date
 * @property int status
 */
class VodacomTransaction extends BaseModel implements RawTransaction
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
