<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;

/**
 * Class TransactionConflicts
 *
 * @package  App
 * @property int id
 * @property string state
 */
class TransactionConflicts extends BaseModel
{
    public function transaction()
    {
        return $this->morphTo();
    }
}
