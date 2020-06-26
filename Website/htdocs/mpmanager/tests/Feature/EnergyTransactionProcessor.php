<?php


namespace Tests\Feature;


use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;
use App\Models\Person\Person;
use App\Models\SocialTariff;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EnergyTransactionProcessor extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function withValidData()
    {

        Queue::fake();

        //create person
        factory(Person::class)->create();
        //create meter-tariff
        factory(MeterTariff::class)->create();

        MeterTariff::first()->socialTariff()->create([
            'tariff_id' => 1,
            'daily_allowance' => 10,
            'price' => 10000,
            'initial_energy_budget' => 10,
            'maximum_stacked_energy' => 70,
        ]);


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
            'serial_number' => '4700005646',
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

        MeterParameter::query()->first()->socialTariffPiggyBank()->create([
            'savings' => SocialTariff::query()->first()->initial_energy_budget,
            'social_tariff_id' => SocialTariff::query()->first()->id,
        ]);

        factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->message = '47000319492';
        $transaction->amount = 1000;

        $vodacomTransaction = VodacomTransaction::query()->first();
        $vodacomTransaction->transaction()->save($transaction);
        $eTP = new \App\Jobs\EnergyTransactionProcessor($transaction);
        $eTP->handle();


        $this->assertCount(1, \App\Models\SocialTariffPiggyBank::all());
        $this->assertEquals(1.01, $eTP->transactionData->chargedEnergy);

    }

}
