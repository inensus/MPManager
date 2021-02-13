<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\ConnectionGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
