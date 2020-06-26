<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Meter\Meter::class, function (Faker $faker) {
    return [
        'meter_type_id' => $faker->randomNumber(1),
        'in_use' => false,
        'manufacturer_id' => 1,
        'serial_number' => str_random(36),
    ];
});
