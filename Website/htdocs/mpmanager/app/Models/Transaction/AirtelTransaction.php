<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;

/**
 * Class AirtelTransaction
 * @package App
 * @property int $id
 * @property string $interface_id
 * @property string $business_number
 * @property string $trans_id
 * @property int $status
 * @property string $tr_id
 */
class AirtelTransaction extends BaseModel implements RawTransaction
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
