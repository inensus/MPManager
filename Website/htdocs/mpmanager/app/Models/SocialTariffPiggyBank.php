<?php

namespace App\Models;

use App\Models\Meter\MeterParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SocialTariffPiggyBank
 * @package App\Models
 *
 * @property int $owner_id
 * @property string $owner_type
 * @property int $savings
 */
class SocialTariffPiggyBank extends Model
{
    protected $guarded = [];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(MeterParameter::class);
    }

    public function socialTariff()
    {
        return $this->belongsTo(SocialTariff::class);
    }
}
