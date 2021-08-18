<?php

namespace Tests\Feature;

use App\Misc\TransactionDataContainer;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Person\Person;
use Database\Factories\AddressFactory;
use Database\Factories\CityFactory;
use Database\Factories\ClusterFactory;
use Database\Factories\MeterTariffFactory;
use Database\Factories\MeterTypeFactory;
use Database\Factories\MiniGridFactory;
use Database\Factories\PersonFactory;
use Database\Factories\TransactionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class CalinApiTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * You need to have a valid registered meter by CALIN
     * for security reasons we can not provide a meter to perfom the test
     * This test passed on 28.06.2020 with a valid meter
     */
    public function test_generate_token_for_a_valid_meter(): void
    {
        Bus::fake();
        $this->withoutExceptionHandling();

        $this->initializeData();
        $transaction = TransactionFactory::new()->make();
        $api = app()->make(Manufacturer::query()->first()->api_name);
        $token = $api->chargeMeter(TransactionDataContainer::initialize($transaction));
        $this->assertArrayHasKey('token', $token);
        $this->assertArrayHasKey('energy', $token);

    }

    private function initializeData()
    {
        ClusterFactory::new()->create();
        MiniGridFactory::new()->create();
        CityFactory::new()->create();
        AddressFactory::new()->create();
        PersonFactory::new()->create();
        MeterTariffFactory::new()->create();
        MeterTypeFactory::new()->create();

        Manufacturer::query()->create([
            'name' => 'CALIN',
            'website' => 'http://www.calinmeter.com/',
            'api_name' => 'CalinApi',
        ]);

        //create meter
        Meter::query()->create([
            'serial_number' => '47000268748',
            'meter_type_id' => 1,
            'in_use' => 1,
            'manufacturer_id' => 1,
        ]);

        $p = Person::query()->first();
        $p->meters()->create([
            'tariff_id' => 1,
            'meter_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);
    }
}
