<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 03.07.18
 * Time: 17:20
 */


use App\Models\AccessRate\AccessRate;
use App\Models\AccessRate\AccessRatePayment;
use App\Models\Manufacturer;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use Carbon\Carbon;
use Faker\Generator as Faker;

//Manufacturer
$factory->define(Manufacturer::class, function(Faker $faker){
    return [
        'name' => 'Calin',
        'website'=> $faker->domainName(),
        'api_name' => 'CalinApi'
    ];
});
//MeterType
$factory->define(MeterType::class, function(Faker $faker){
    return [
        'online' => $faker->numberBetween(0,1),
        'phase' => 1,
        'max_current' => 10,
    ];
});

//tariff
$factory->define(MeterTariff::class, function(Faker $faker){
    return [
        'name' => $faker->name,
        'price' => $faker->numberBetween(100000,150000),
        'currency' => 'TZS',
        'factor' => $faker->numberBetween(0,5),
    ];
});
$factory->define(AccessRate::class, function(Faker $faker){
    return [
        'amount' => $faker->numberBetween(7500,15000),
        'period' => $faker->numberBetween(7,30),
    ];
});

//Person
$factory->define(Person::class, function(Faker $faker){
    return [
        'title' => $faker->title('male'),
        'name' => $faker->firstName(),
        'surname' => $faker->firstName(),
        'birth_date' => $faker->date(),
        'sex' => 'male',
        ];

});
$factory->define(AccessRatePayment::class, function(Faker $faker){
    return [
        'debt'=> 0,
        'due_date'=> Carbon::now()->addDays(7),
    ];
});

$factory->define(VodacomTransaction::class, function (Faker $faker){

    return [
        'conversation_id' => $faker->unique()->randomNumber(),
        'originator_conversation_id' => $faker->unique(true)->randomNumber(),
        'mpesa_receipt' => $faker->name(),
        'transaction_date' => $faker->dateTime(),
        'transaction_id' => $faker->unique()->randomNumber(),
        'status' => 0,
    ];
});

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'amount' =>  $faker->unique()->randomNumber(),
        'sender' => $faker->phoneNumber,
        'message' => '47000268748',
    ];
});
