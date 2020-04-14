<?php

use Faker\Generator as Faker;

$factory->define(App\Meter::class, function (Faker $faker) {
    return [
        'meter_type_id' => $faker->randomNumber(1),
        'in_use' => $faker->boolean(),
        'manufacturer_id' => 1,
        'serial_number' => str_random(36),
    ];
});
