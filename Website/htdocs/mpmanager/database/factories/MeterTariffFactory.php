<?php

use App\Models\Meter\MeterTariff;
use Faker\Generator as Faker;

$factory->define(MeterTariff::class, static function (Faker $faker) {

    return [
        'name' => $faker->name,
        'price' => 100000,
        'total_price' => 100000,
        'currency' => 'EUR',
    ];
});
