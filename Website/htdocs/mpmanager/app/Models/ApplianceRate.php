<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Class ApplianceRate
 * @package App\Models
 *
 * @property $appliance_person_id int
 * @property $rate_cost int
 * @property $remaining int
 * @property $due_date string
 *
 */
class ApplianceRate extends Model
{
    //
    protected $fillable = [
        'appliance_person_id',
        'rate_cost',
        'remaining',
        'due_date',
    ];

    public function appliancePerson(): BelongsTo
    {
        return $this->belongsTo(AppliancePerson::class);
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'affected');
    }


    public function paymentHistory(): MorphOne
    {
        return $this->morphOne(PaymentHistory::class, 'paid_for');
    }
}
