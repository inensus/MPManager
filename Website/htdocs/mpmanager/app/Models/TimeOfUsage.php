<?php

namespace App\Models;

use App\Models\Meter\MeterTariff;


class TimeOfUsage extends BaseModel
{
    public function tariff()
    {
        return $this->belongsTo(MeterTariff::class);
    }
}
