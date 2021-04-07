<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use App\Models\MainSettings as MainSettingsAlias;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(MainSettingsAlias::class, function (Faker $faker) {
    return [
        'site_title' => 'MPM - The easiest way to manage your Mini-Grid',
        'company_name' => 'MicroPowerManager',
        'currency' => '€',
        'country' => 'Germany',
        'vat_energy' => 1,
        'vat_appliance' => 18,
        'language' => 'en',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
