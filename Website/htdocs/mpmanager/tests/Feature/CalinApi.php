<?php

namespace Tests\Feature;


use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;
use App\Models\Person\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CalinApi extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * @test
     * You need to have a valid registered meter by CALIN
     * for security reasons we can not provide a meter to perfom the test
     * This test passed on 28.06.2020 with a valid meter
     */
    public function generateTokenForValidMeter()
    {

        $this->withoutExceptionHandling();

        //create person
        factory(Person::class)->create();
        //create meter-tariff
        factory(MeterTariff::class)->create();

        //create meter-type
        MeterType::query()->create([
            'online' => 0,
            'phase' => 1,
            'max_current' => 10,
        ]);

        //create calin manufacturer
        Manufacturer::query()->create([
            'name' => 'CALIN',
            'website' => 'http://www.calinmeter.com/',
            'api_name' => 'CalinApi',
        ]);

        //create meter
        Meter::query()->create([
            'serial_number' => '4700005646',
            'meter_type_id' => 1,
            'in_use' => 1,
            'manufacturer_id' => 1,
        ]);

        //associate meter with a person
        $p = Person::query()->first();
        $p->meters()->create([
            'tariff_id' => 1,
            'meter_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);

        $api = app()->make(Manufacturer::query()->first()->api_name);
        $token = $api->generateToken(Meter::query()->first(), 1);
        $this->assertArrayHasKey('token', $token);
        $this->assertArrayHasKey('energy', $token);

    }


}
