<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 13.07.18
 * Time: 09:40
 */

namespace App\Http\Services;

use App\Models\Country;

class CountryService
{
    public function __construct(private Country $country)
    {
    }

    public function getByCode(string $countryCode)
    {
        return $this->country->where('country_code', $countryCode)->first();
    }
}
