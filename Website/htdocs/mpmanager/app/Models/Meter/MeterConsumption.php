<?php

namespace App\Models\Meter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MeterConsumption
 *
 * @package  App\Models\Meter
 * @property int $id
 * @property int $meter_id
 * @property double $total_consumption
 * @property double $daily_consumption
 * @property double $credit_on_meter
 * @property string $reading_date
 */
class MeterConsumption extends Model
{
    protected $table = 'meter_consumptions';

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }


    public function __toString()
    {
        return 'Meter  : ' . $this->meter_id . '  consumption : ' . $this->total_consumption .
            '  credit :' . $this->credit_on_meter;
    }
}
