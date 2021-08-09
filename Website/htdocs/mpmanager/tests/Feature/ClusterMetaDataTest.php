<?php

namespace Tests\Unit;

use App\Models\ClusterMetaData;
use App\Models\Meter\MeterParameter;
use App\Models\MiniGrid;
use App\Models\Person\Person;
use Database\Factories\AddressFactory;
use Database\Factories\CityFactory;
use Database\Factories\ClusterFactory;
use Database\Factories\MeterFactory;
use Database\Factories\MeterParameterFactory;
use Database\Factories\MeterTariffFactory;
use Database\Factories\MiniGridFactory;
use Database\Factories\PersonFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClusterMetaDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_cluster_meta_data_object_after_added_a_miniGrid()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->mini_grid_id);

    }

    public function test_delete_cluster_meta_data_object_after_deleted_a_miniGrid()
    {
        $this->initializeData();
        $mini_grid = MiniGrid::query()->latest()->first();
        $mini_grid->delete();
        $cluster_meta_data_count = ClusterMetaData::all()->count();
        $this->assertEquals(0,$cluster_meta_data_count);

    }

    public function test_increase_connected_meters_count_after_created_new_meter()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->connected_meters);
    }

    public function test_increase_registered_customers_count_after_add_new_customer()
    {
        $this->initializeData();

        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(1,$cluster_meta_data->registered_customers);
    }

    public function test_decrease_connected_meters_count_after_deleted_a_meter()
    {
        $this->initializeData();

        $meterParameter = MeterParameter::query()->latest()->first();
        $meterParameter->delete();
        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(0,$cluster_meta_data->connected_meters);
    }

    public function test_decrease_registered_customers_count_after_deleted_a_customer()
    {
        //create cluster
        ClusterFactory::new()->create();
        //create mini-grid
        MiniGridFactory::new()->create();
        //create city
        CityFactory::new()->create();
        //create address
        AddressFactory::new()->create();
        //create person
        PersonFactory::new()->create();

        $person = Person::query()->latest()->first();
        $person->delete();
        $cluster_meta_data = ClusterMetaData::query()->latest()->first();
        $this->assertEquals(0,$cluster_meta_data->registered_customers);
    }

    private function initializeData()
    {
        //create cluster
        ClusterFactory::new()->create();
        //create mini-grid
        MiniGridFactory::new()->create();
        //create city
        CityFactory::new()->create();
        //create address
        AddressFactory::new()->create();
        //create person
        PersonFactory::new()->create(['is_customer' => 1]);
        MeterTariffFactory::new()->create();
        //create meter
        MeterFactory::new()->create();
        //create meterParameter
        MeterParameterFactory::new()->create();
    }
}
