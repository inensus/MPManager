<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Meter\MeterType;
use Faker\Generator as Faker;

$factory->define(MeterType::class, function (Faker $faker) {
    return [
        'online' => 0,
        'phase' => 1,
        'max_current' => 10,
    ];
});
