<?php


namespace Tests\Feature;


use App\Models\Cluster;
use App\Models\GeographicalInformation;
use App\Models\MiniGrid;
use App\Models\Solar;
use App\Models\WeatherData;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SolarResource extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function createSolarEntry(): void
    {


        //create cluster
        Cluster::create(['name' => 'Test Cluster', 'manager_id' => 1]);
        //create mini-grid
        $miniGrid = MiniGrid::create(['cluster_id' => 1, 'name' => 'Test-Grid', 'data_stream' => 0]);
        //associate points to grap weather data for its
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


        //check if weather reading file created
        $this->assertCount(1, WeatherData::all());
    }


    /** @test */
    public function listSolarsByMiniGrid()
    {
        $solarToCreate = 2;

        //create cluster
        Cluster::create(['name' => 'Test Cluster', 'manager_id' => 1]);
        //create mini-grid
        $miniGrid = MiniGrid::create(['cluster_id' => 1, 'name' => 'Test-Grid', 'data_stream' => 0]);
        //associate points to grap weather data for its
        $miniGrid->location()->save(GeographicalInformation::make(['points' => '-1.8727924588344,33.022885322571']));

        //create dummy solar entries
        factory(Solar::class)->times($solarToCreate)->create();
        //correctly stored ?
        $this->assertCount($solarToCreate, Solar::all());

        $request = $this->get('/api/mini-grids/1/solar-readings');
        //successful response ?
        $request->assertStatus(200);
        //does it contain all data?
        $this->assertCount($solarToCreate, $request->json('data'));

    }
}
