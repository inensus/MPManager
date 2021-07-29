<?php

namespace Database\Factories;

use App\Models\Meter\MeterParameter;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeterParameterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MeterParameter::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_type' => 'person',
            'owner_id' => 1,
            'meter_id' => 1,
            'tariff_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ];
    }
}
