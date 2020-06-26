<?php


namespace Tests\Feature;


use App\Jobs\CreatePiggyBankEntry;
use App\Jobs\UpdatePiggyBankEntry;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterTariff;
use App\Models\Person\Person;
use App\Models\SocialTariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SocialTariffPiggyBank extends TestCase
{


    use RefreshDatabase, WithFaker;

    /** @test */
    public function addBalance()
    {
        //create tariff
        factory(MeterTariff::class)->create();
        //add social tariff
        MeterTariff::first()->socialTariff()->create([
            'tariff_id' => 1,
            'daily_allowance' => 10,
            'price' => 10000,
            'initial_energy_budget' => 10,
            'maximum_stacked_energy' => 70,
        ]);

        //create meter
        factory(Meter::class)->create();
        //create customer
        factory(Person::class)->create();
        // attach meter to customer with tariff
        Person::first()->meters()->create([
            'meter_id' => 1,
            'tariff_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);


        //create social bank entry manually
        $createPiggyBankJob = new CreatePiggyBankEntry(Person::first()->meters()->first());
        $createPiggyBankJob->handle();


        //run manager job
        $job = new \App\Jobs\SocialTariffPiggyBankManager();

        $socialTariff = SocialTariff::first();
        $socialBank = SocialTariffPiggyBank::first();

        $savings = $socialBank->savings;

        for ($i = 1; $i <= $socialTariff->maximum_stacked_energy / $socialTariff->daily_allowance; $i++) {
            $job->handle();
            if ($i % ($socialTariff->maximum_stacked_energy / $socialTariff->daily_allowance)) {
                $this->assertEquals($savings + ($socialTariff->daily_allowance * $i), $socialBank->fresh()->savings);
            } else {
                $this->assertEquals($savings, $socialTariff->initial_energy_budget);
            }
        }
    }


    /** @test */
    public function changeTariffAndResetSavings()
    {

        Queue::fake();
        //create tariff
        factory(MeterTariff::class)->times(2)->create();
        //add social tariff
        MeterTariff::first()->socialTariff()->create([
            'tariff_id' => 1,
            'daily_allowance' => 10,
            'price' => 10000,
            'initial_energy_budget' => 10,
            'maximum_stacked_energy' => 70,
        ]);


        //create meter
        factory(Meter::class)->create();
        //create customer
        factory(Person::class)->create();
        // attach meter to customer with tariff
        Person::first()->meters()->create([
            'meter_id' => 1,
            'tariff_id' => 2,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);


        $this->assertCount(0, \App\Models\SocialTariffPiggyBank::all());

        $updatePiggyBankJob = new UpdatePiggyBankEntry(Person::first()->meters()->first());
        $updatePiggyBankJob->handle();

        $this->assertCount(0, \App\Models\SocialTariffPiggyBank::all());

        Queue::assertPushed(CreatePiggyBankEntry::class);


        $createPiggyBankJob = new CreatePiggyBankEntry(Person::first()->meters()->first());
        $createPiggyBankJob->handle();

        $this->assertCount(0, \App\Models\SocialTariffPiggyBank::all());


        //update tariff

        $meterParameters = Person::first()->meters()->first();
        $meterParameters->update(['tariff_id' => 1]);

        $updatePiggyBankJob = new UpdatePiggyBankEntry(Person::first()->meters()->first());
        $updatePiggyBankJob->handle();

        $this->assertCount(0, \App\Models\SocialTariffPiggyBank::all());

        Queue::assertPushed(CreatePiggyBankEntry::class);

        $createPiggyBankJob = new CreatePiggyBankEntry(Person::first()->meters()->first());
        $createPiggyBankJob->handle();

        $this->assertCount(1, \App\Models\SocialTariffPiggyBank::all());
    }
}
