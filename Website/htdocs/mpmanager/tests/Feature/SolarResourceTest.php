<?php


namespace Tests\Feature;


use App\Models\Cluster;
use App\Models\GeographicalInformation;
use App\Models\MiniGrid;
use App\Models\Solar;
use App\Models\WeatherData;
use Carbon\Carbon;
use Database\Factories\SolarFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SolarResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_solar_entry(): void
    {
        Cluster::create(['name' => 'Test Cluster', 'manager_id' => 1]);
        $miniGrid = MiniGrid::create(['cluster_id' => 1, 'name' => 'Test-Grid', 'data_stream' => 0]);
        $miniGrid->location()->save(GeographicalInformation::make(['points' => '-1.8727924588344,33.022885322571']));
        $requestData = [
            'node_id' => 1,
            'device_id' => 'faker-node',
            'mini_grid_id' => 1,
            'solar_reading' =>
                [
                    'starting_time' => '2020-06-17 18:22:30',
                    'readings' => 300,
                    'total' => 123,
                    'average' => 412,
                ],
            'time_stamp' => Carbon::now()->timestamp,

        ];
        $request = $this->post('/api/solar', $requestData, ['accept' => 'application/json']);
        $request->assertStatus(201);
        $this->assertCount(1, WeatherData::all());
    }


    public function test_list_solars_by_mini_grid()
    {
        $solarToCreate = 2;
        Cluster::create(['name' => 'Test Cluster', 'manager_id' => 1]);
        $miniGrid = MiniGrid::create(['cluster_id' => 1, 'name' => 'Test-Grid', 'data_stream' => 0]);
        $miniGrid->location()->save(GeographicalInformation::make(['points' => '-1.8727924588344,33.022885322571']));

        SolarFactory::times($solarToCreate)->create();create();
        $this->assertCount($solarToCreate, Solar::all());

        $request = $this->get('/api/mini-grids/1/solar-readings');
        $request->assertStatus(200);
        $this->assertCount($solarToCreate, $request->json('data'));

    }
}
