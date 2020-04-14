<?php

namespace App\Models\Loan;

use App\Models\PaymentHistory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{

    //related payment histories which are made for that loan
    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class);
    }
}
