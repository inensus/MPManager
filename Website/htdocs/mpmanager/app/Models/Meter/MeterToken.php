<?php

namespace App\Models\Meter;

use App\Models\BaseModel;
use App\Models\PaymentHistory;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;

/**
 * Class Token
 * @package App
 * @property string token
 * @property double energy
 */
class MeterToken extends BaseModel
{

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function __toString()
    {
        return 'Token: ' . $this->token . ' for ' . $this->energy . 'kWh';
    }

    public function paymentHistories(): MorphOne
    {
        return $this->morphOne(PaymentHistory::class, 'paid_for');
    }


    public function soldEnergyPerPeriod($startDate, $endDate)
    {
        return $this::select(DB::raw(" SUM( energy) as sold,YEARWEEK(created_at,3) as period"))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('YEARWEEK(created_at,3)'));
    }
}
