<?php

namespace App\Models\Meter;

use App\Models\AccessRate\AccessRate;
use App\Models\BaseModel;
use App\Models\CustomerGroup;
use App\Models\SocialTariff;
use App\Models\TariffPricingComponent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Tariff
 *
 * @package App
 *
 * @property string $name
 * @property int $price (100 times the price. Allows to play with .00 decimals)
 * @property int $total_price (100 times the price. Allows to play with .00 decimals)
 * @property string $currency
 * @property int|null $factor
 */
class   MeterTariff extends BaseModel
{
    protected $table = 'meter_tariffs';
    protected $guarded = [];

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

    public function pricingComponent(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(TariffPricingComponent::class, 'owner');
    }

    public function socialTariff()
    {
        return $this->hasOne(SocialTariff::class, 'tariff_id');
    }
}
