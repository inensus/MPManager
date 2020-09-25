<?php

namespace App\Models;

use App\Models\Meter\MeterTariff;

class ElasticUsageTime extends BaseModel
{
    public function tariff()
    {
        return $this->belongsTo(MeterTariff::class);
    }
}
