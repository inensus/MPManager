<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\Manufacturer;
use Faker\Generator as Faker;

$factory->define(Manufacturer::class, function (Faker $faker) {
    return [
        'name' => 'CALIN',
        'website' => 'http://www.calinmeter.com/',
        'api_name' => 'CalinApi',
    ];
});
