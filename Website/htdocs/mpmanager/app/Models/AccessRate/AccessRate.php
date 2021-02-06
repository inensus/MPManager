<?php

namespace App\Models\AccessRate;

use App\Models\BaseModel;
use App\Models\Meter\MeterTariff;

/**
 * Class AccessRate
 *
 * @package  App
 * @property int id
 * @property int amount
 * @property int tariff_id
 * @property int period when the payment repeats itself
 */
class AccessRate extends BaseModel
{
    public function tariff()
    {
        return $this->belongsTo(MeterTariff::class, 'tariff_id', 'id');
    }

    public function accessRatePayments()
    {
        return $this->hasMany(AccessRatePayment::class);
    }

    public function __toString()
    {
        return sprintf('For tariff : %s', $this->tariff()->first()->name);
    }

    public function paymentHistories()
    {
        return $this->morphMany(PaymentHistory::class, 'paid_for');
    }
}
