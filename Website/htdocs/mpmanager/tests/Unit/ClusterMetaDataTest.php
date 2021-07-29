<?php

namespace Tests\Unit;

use App\Models\Address\Address;
use App\Models\City;
use App\Models\Cluster;
use App\Models\ClusterMetaData;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\MiniGrid;
use App\Models\Person\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClusterMetaDataTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function create_cluster_meta_data_object_after_added_a_miniGrid()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->mini_grid_id);

    }

    /** @test */
    public function delete_cluster_meta_data_object_after_deleted_a_miniGrid()
    {
        $this->initializeData();
        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $cluster_meta_data->delete();
        $cluster_meta_data_count = ClusterMetaData::all()->count();
        $this->assertEquals(0,$cluster_meta_data_count);

    }

    /** @test */
    public function increment_connected_meters_count_after_created_new_meter()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->connected_meters);
    }

    /** @test */
    public function increment_registered_customers_count_after_add_new_customer()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->registered_customers);
    }

    /** @test */
    public function decrement_connected_meters_count_after_deleted_a_meter()
    {
        $this->initializeData();

        $meterParameter = MeterParameter::query()->latest()->first();
        $meterParameter->delete();
        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(0,$cluster_meta_data->connected_meters);
    }

    /** @test */
    public function decrement_registered_customers_count_after_deleted_a_customer()
    {
        $this->initializeData();

        $person = Person::query()->latest()->first();
        $person->delete();
        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(0,$cluster_meta_data->registered_customers);
    }

    private function initializeData()
    {
        //create cluster
        Cluster::query()->create(['name' => 'Test Cluster', 'manager_id' => 1]);

        //create mini-grid
        MiniGrid::query()->create(['cluster_id' => 1, 'name' => 'Test-Grid', 'data_stream' => 0]);

        //create city
        City::query()->create(['name' => 'test', 'country_id' => 1, 'cluster_id' => 1, 'mini_grid_id' => 1]);

        //create address
        Address::query()->create([
            'phone' => '+905494322161',
            'is_primary' => 1,
            'owner_type' => 'person',
            'owner_id' => 1,
            'city_id' => 1
        ]);

        //create person
        factory(Person::class)->create();

        //create meter
        Meter::query()->create([
            'serial_number' => '4700005646',
            'meter_type_id' => 1,
            'in_use' => 0,
            'manufacturer_id' => 1
        ]);

        //create meterParameter
        MeterParameter::query()->create([
            'owner_type' => 'person',
            'owner_id' => 1,
            'meter_id' => 1,
            'tariff_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);

    }
}
