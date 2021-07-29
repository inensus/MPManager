<?php

namespace Database\Factories;

use App\Models\MiniGrid;
use Illuminate\Database\Eloquent\Factories\Factory;

class MiniGridFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MiniGrid::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cluster_id' => 1,
            'name' => 'Test-Grid',
            'data_stream' => 0
        ];
    }
}
