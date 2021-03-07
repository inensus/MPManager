<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SmsResendInformationKey;
use Faker\Generator as Faker;

$factory->define(SmsResendInformationKey::class, function (Faker $faker) {
    return [
        'key' => 'Resend'
    ];
});
