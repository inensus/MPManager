<?php


namespace Tests\Feature;


use App\Jobs\SmsProcessor;
use App\Misc\TransactionDataContainer;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterToken;
use App\Models\Meter\MeterType;
use App\Models\PaymentHistory;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TokenProcessor extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function withValidTransaction()
    {
        Queue::fake();

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

        factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->message = '4700005646';

        $vodacomTransaction = VodacomTransaction::query()->first();
        $vodacomTransaction->transaction()->save($transaction);


        $transactionContainer = TransactionDataContainer::initialize(Transaction::query()->first());
        $transactionContainer->chargedEnergy = 1;
        $tp = new \App\Jobs\TokenProcessor(
            app()->make(Manufacturer::query()->first()->api_name),
            $transactionContainer
        );

        $tp->handle();

        $this->assertCount(1, MeterToken::all());
        $this->assertCount(1, PaymentHistory::all());
        Queue::assertPushed(SmsProcessor::class);

    }
}
