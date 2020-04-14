<?php

namespace App\Models\Meter;

use App\Models\AccessRate\AccessRate;
use App\Models\BaseModel;
use App\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Tariff
 *
 * @package App
 *
 * @property string $name
 * @property int $price (10 times the price. Allows to play with .00 decimals)
 * @property string $currency
 * @property int|null $factor
 */
class MeterTariff extends BaseModel
{
    public function meterParameter(): HasMany
    {
        return $this->hasMany(MeterParameter::class, 'tariff_id');
    }

    public function meterParametersCount()
    {
        return $this->meterParameter()
            ->selectRaw('tariff_id, count(*) as aggregate')
            ->groupBy('tariff_id');
    }

    public function customerGroups(): HasMany
    {
        return $this->hasMany(CustomerGroup::class, 'tariff_id');
    }


    public function accessRate(): HasOne
    {
        return $this->hasOne(AccessRate::class, 'tariff_id');
    }
}
