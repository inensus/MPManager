<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ConnectionType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
