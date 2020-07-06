<?php

namespace App\Models;

use App\Events\AppliancePersonCreated;
use App\Models\Person\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


/**
 * Class AppliancePerson
 * @package App\Models
 *
 *
 * @property int $appliance_type_id
 * @property int $person_id
 * @property int $total_cost
 * @property int $rate_count
 */
class AppliancePerson extends Model
{

    protected $dispatchesEvents = [
        'created' => AppliancePersonCreated::class,
    ];
    //
    protected $fillable = [
        'person_id',
        'appliance_type_id',
        'total_cost',
        'rate_count',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'affected');
    }


    public function applianceType(): BelongsTo
    {
        return $this->belongsTo(ApplianceType::class, 'appliance_type_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(ApplianceRate::class);
    }
}
