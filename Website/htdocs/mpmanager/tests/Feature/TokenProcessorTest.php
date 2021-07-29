<?php


namespace Tests\Feature;


use App\Jobs\SmsProcessor;
use App\Jobs\TokenProcessor;
use App\Misc\TransactionDataContainer;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterToken;
use App\Models\Meter\MeterType;
use App\Models\PaymentHistory;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use App\Sms\Senders\SmsConfigs;
use App\Sms\SmsTypes;
use Database\Factories\MeterTariffFactory;
use Database\Factories\PersonFactory;
use Database\Factories\TransactionFactory;
use Database\Factories\VodacomTransactionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TokenProcessorTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function test_with_valid_transaction()
    {
        Queue::fake();
        PersonFactory::new()->create();
        MeterTariffFactory::new()->create();
        MeterType::create([
            'online' => 0,
            'phase' => 1,
            'max_current' => 10,
        ]);

        Manufacturer::create([
            'name' => 'CALIN',
            'website' => 'http://www.calinmeter.com/',
            'api_name' => 'CalinApi',
        ]);

        Meter::create([
            'serial_number' => '4700005646',
            'meter_type_id' => 1,
            'in_use' => 1,
            'manufacturer_id' => 1,
        ]);
        $p = Person::first();
        $p->meters()->create([
            'tariff_id' => 1,
            'meter_id' => 1,
            'connection_type_id' => 1,
            'connection_group_id' => 1,
        ]);

        VodacomTransactionFactory::new()->create();
        $transaction = TransactionFactory::new()->make();
        $transaction->message = '4700005646';

        $vodacomTransaction = VodacomTransaction::query()->first();
        $vodacomTransaction->transaction()->save($transaction);

        $transactionContainer = TransactionDataContainer::initialize(Transaction::query()->first());
        $transactionContainer->chargedEnergy = 1;
        $tp = new TokenProcessor($transactionContainer);
        $tp->handle();
        SmsProcessor::dispatch(
            $transaction,
            SmsTypes::TRANSACTION_CONFIRMATION,
            SmsConfigs::class);
        $this->assertCount(1, MeterToken::all());
        $this->assertCount(1, PaymentHistory::all());
        Queue::assertPushed(SmsProcessor::class);
    }
}
