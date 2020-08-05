<?php

namespace App\Models;

use App\Events\AssetPersonCreated;
use App\Models\Person\Person;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;


/**
 * Class AssetPerson
 * @package App\Models
 *
 *
 * @property int $asset_type_id
 * @property int $person_id
 * @property int $total_cost
 * @property int $rate_count
 */
class AssetPerson extends Model
{

    protected $dispatchesEvents = [
        'created' => AssetPersonCreated::class,
    ];
    //
    protected $fillable = [
        'person_id',
        'asset_type_id',
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


    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(AssetRate::class);
    }
}
