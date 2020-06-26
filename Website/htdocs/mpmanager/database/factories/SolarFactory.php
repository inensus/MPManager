<?php


use App\Models\Solar;
use Faker\Generator as Faker;

$factory->define(Solar::class, static function (Faker $faker) {
    return [

        'node_id' => 1,
        'device_id' => 'test-case',
        'mini_grid_id' => 1,
        'time_stamp' => $faker->dateTime(),
        'starting_time' => $faker->dateTime('-5 minutes'),
        'ending_time' => $faker->dateTime(),
        'min' => $faker->numberBetween(0, 100),
        'max' => $faker->numberBetween(0, 100),
        'average' => $faker->numberBetween(0, 100),
        'duration' => 300,
        'readings' => $faker->numberBetween(200, 300),

    ];
});
