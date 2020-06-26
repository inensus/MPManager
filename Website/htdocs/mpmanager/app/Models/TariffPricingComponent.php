<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class TariffPricingComponent
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int $price
 * @property int $owner_id
 * @property string $owner_type
 */
class TariffPricingComponent extends Model
{
    protected $guarded = [];

    public function owner()
    {
        return $this->morphTo();
    }

}
