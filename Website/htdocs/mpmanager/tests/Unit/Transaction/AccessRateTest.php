<?php

namespace Tests\Unit;

use App\Misc\TransactionDataContainer;
use App\Models\AccessRate\AccessRate;
use App\Models\AccessRate\AccessRatePayment;
use App\Models\Address\Address;
use App\Models\City;
use App\Models\Cluster;
use App\Models\ConnectionGroup;
use App\Models\ConnectionType;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;
use App\Models\MiniGrid;
use App\Models\Person\Person;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AccessRateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function initApplication(): void
    {
        Bus::fake();

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
        $customer = factory(Person::class)->create();
        $manufacturer = factory(Manufacturer::class)->create();
        $meterType = factory(MeterType::class)->create();
        $connectionType = factory(ConnectionType::class)->create();
        $connectionGroup = factory(ConnectionGroup::class)->create();
        $meter = new Meter();
        $meter->serial_number = '47000268748';
        $meter->meterType()->associate($meterType);
        $meter->manufacturer()->associate($manufacturer);
        $meter->in_use = 1;
        $meter->save();
        $tariff = factory(MeterTariff::class)->create();
        $accessRate = factory(AccessRate::class)->make();
        $tariff->accessRate()->save($accessRate);
        $meterParameter = new MeterParameter();
        $meterParameter->tariff()->associate($tariff);
        $meterParameter->meter()->associate($meter);
        $meterParameter->connectionGroup()->associate($connectionGroup);
        $meterParameter->connectionType()->associate($connectionType);
        $meter->in_use = 1;
        $meter->save();
        $meterParameter->owner()->associate($customer);
        $meterParameter->save();
        $accessRatePayment = factory(AccessRatePayment::class)->make();
        $accessRatePayment->meter()->associate($meter);
        $accessRatePayment->accessRate()->associate($accessRate);
        $accessRatePayment->save();
    }


    /**
     * @throws \Exception
     */
    public function testWithNoAccessRate(): void
    {

        $this->initApplication();
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);
        $transactionContainer = TransactionDataContainer::initialize($transaction);
        $accessRateDebt = \App\PaymentHandler\AccessRate::payAccessRate($transactionContainer);
        $this->assertEquals(0, $accessRateDebt);

    }

    public function testWithCoverAccessRate(): void
    {
        $this->initApplication();
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);
        $transactionContainer = TransactionDataContainer::initialize($transaction);
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 100;
        $accessRatePayment->save();
        $accessRateDebt = \App\PaymentHandler\AccessRate::payAccessRate($transactionContainer);
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $this->assertEquals(100, $accessRateDebt);
        $this->assertEquals(0, $accessRatePayment->debt);
    }

    public function testWithNonCoverAccessRate(): void
    {
        $this->initApplication();
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);
        $transactionContainer = TransactionDataContainer::initialize($transaction);
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 1000;
        $accessRatePayment->save();
        $accessRateDebt = \App\PaymentHandler\AccessRate::payAccessRate($transactionContainer);
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $this->assertEquals(500, $accessRateDebt);
        $this->assertEquals(500, $accessRatePayment->debt);
    }

    public function testWithExactlyAccessRateDebt()
    {
        $this->initApplication();
        //fake Transaction
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->amount = 1000;
        $vodacomTransaction->transaction()->save($transaction);

        $transactionContainer = TransactionDataContainer::initialize($transaction);

        //with 100 access rate
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 1000;
        $accessRatePayment->save();

        $accessRateDebt = \App\PaymentHandler\AccessRate::payAccessRate($transactionContainer);

        //get access rate payment again
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();

        $this->assertEquals(1000, $accessRateDebt);
        $this->assertEquals(0, $accessRatePayment->debt);
    }

}
