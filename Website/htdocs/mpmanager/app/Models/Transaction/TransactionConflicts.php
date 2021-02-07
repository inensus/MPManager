<?php

namespace App\Models\Transaction;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class TransactionConflicts
 *
 * @package  App
 * @property int id
 * @property string state
 */
class TransactionConflicts extends BaseModel
{
    public function transaction(): MorphTo
    {
        return $this->morphTo();
    }
}
