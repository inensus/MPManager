<?php

namespace App\Models\AccessRate;

use App\Models\Meter\Meter;
use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AccessRatePayment
 *
 * @package  App
 * @property int meter_id
 * @property int access_rate_id
 * @property DateTime due_date
 * @property int debt
 */
class AccessRatePayment extends Model
{
    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function accessRate()
    {
        return $this->belongsTo(AccessRate::class);
    }
}
