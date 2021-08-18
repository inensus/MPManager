<?php

namespace Database\Factories;

use App\Models\Model;
use Faker\Provider\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Address\Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone' => '+905494322161',
            'is_primary' => 1,
            'owner_type' => 'person',
            'owner_id' => 1,
            'city_id' => 1
        ];
    }
}
