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
        MeterType::create([
            'online' => 0,
            'phase' => 1,
            'max_current' => 10,
        ]);

        //create calin manufacturer
        Manufacturer::create([
            'name' => 'CALIN',
            'website' => 'http://www.calinmeter.com/',
            'api_name' => 'CalinApi',
        ]);

        //create meter
        Meter::create([
            'serial_number' => '47000319492',
            'meter_type_id' => 1,
            'in_use' => 1,
            'manufacturer_id' => 1,
        ]);

        //associate meter with a person
        $p = Person::first();
        $p->meters()->create([
            'tariff_id' => 1,
            'meter_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);

        $api = app()->make(Manufacturer::first()->api_name);
        $token = $api->generateToken(Meter::first(), 1);
        $this->assertArrayHasKey('token', $token);
        $this->assertArrayHasKey('energy', $token);

    }


}
