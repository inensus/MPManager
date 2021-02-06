<?php

namespace App\Models;

class Country extends BaseModel
{

    public function getRouteKeyName()
    {
        return 'country_code';
    }


    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
