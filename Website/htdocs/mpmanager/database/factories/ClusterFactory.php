<?php

namespace Database\Factories;

use App\Models\Cluster;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClusterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cluster::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Test Cluster',
            'manager_id' => 1,
            'geo_data' => '{"leaflet_id":903,"type":"manual","geojson":{"type":"Polygon",
            "coordinates":[[[-3.204747603780925,37.937924389032375],
            [-3.4220930701917984,37.93779565098191],
            [-3.2492230959644415,38.24208948955007]]]},
            "searched":false,"display_name":"test","selected":true,"draw_type":"draw","lat":-3.2920212566457216,"lon":38.039269843188116}'
        ];
    }
}
