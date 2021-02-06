<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;

/**
 * Class GeographicalInformation
 *
 * @package App\Models
 *
 * @property string $points
 */
class GeographicalInformation extends BaseModel
{
    protected $table = 'geographical_informations';

    public function owner()
    {
        return $this->morphTo();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    public function meters()
    {
        return $this->hasMany(MeterParameter::class);
    }

    public function people()
    {
        return $this->hasMany(Person::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
