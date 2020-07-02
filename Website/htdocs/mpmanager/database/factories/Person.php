<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Person\Person::class, function (Faker $faker) {

    return [
        'name' => $faker->firstName,
        'surname' => $faker->lastName,
        'sex' => 1,
        'is_customer' => 1,
    ];
});
