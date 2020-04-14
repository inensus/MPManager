<?php

namespace Tests\Unit;

use App\AccessRatePayment;
use App\Meter;
use App\MeterParameter;
use App\MeterTariff;
use App\MeterType;
use App\Misc\TransactionDataContainer;
use App\Models\Manufacturer;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;
use App\PaymentHandler\AccessRate;
use App\Person;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessRateTest extends TestCase
{

    private function initApplication()
    {
        $role = new PersonRole();
        $role->name = 'customer';
        $role->save();

        //new manufacturer
        $manufacturer = factory(Manufacturer::class)->create();
        //new meter type
        $meterType = factory(MeterType::class)->create();

        //new meter
        $meter = new Meter();
        $meter->serial_number = '47000268748';
        $meter->meterType()->associate($meterType);
        $meter->manufacturer()->associate($manufacturer);
        $meter->in_use = 1;
        $meter->save();
        //new tariff
        $tariff = factory(MeterTariff::class)->create();

        // new access rate
        $accessRate = factory(\App\AccessRate::class)->make();

        //assign access rate to tariff
        $tariff->accessRate()->save($accessRate);


        //new customer
        $customer = factory(Person::class)->create();
        $customer->roles()->attach($role);

        // new meter parameter
        $meterParameter = new MeterParameter();
        $meterParameter->tariff()->associate($tariff);
        $meterParameter->meter()->associate($meter);
        $meter->in_use = 1;
        $meter->save();
        $meterParameter->owner()->associate($customer);
        $meterParameter->save();


        //new access rate payment
        /**
         * @var AccessRatePayment $accessRatePayment
         */
        $accessRatePayment = factory(AccessRatePayment::class)->make();
        $accessRatePayment->meter()->associate($meter);
        $accessRatePayment->accessRate()->associate($accessRate);
        $accessRatePayment->save();

    }

    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function testWithNoAccessRate()
    {
        $this->initApplication();
        //fake Transaction
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);

        $transactionContainer = TransactionDataContainer::initialize($transaction);
        $result = AccessRate::payAccessRate($transactionContainer);

        //no access rate
        $this->assertEquals($result, 500);

    }
    public function testWithCoverableAccessRate()
    {
        $this->initApplication();
        //fake Transaction
        $vodacomTransaction = factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        //sent amount
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);

        //collect necessary data for handling transaction
        $transactionContainer = TransactionDataContainer::initialize($transaction);

        //with 100 access rate debt
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 100;
        $accessRatePayment->save();

        $result = AccessRate::payAccessRate($transactionContainer);

        //get access rate payment again
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();

        // available money for energy
        $this->assertEquals($result, 400);
        // rest access rate debt
        $this->assertEquals($accessRatePayment->debt, 0);
    }

    public function testWithUnCoverableAccessRate()
    {
        $this->initApplication();
        //fake Transaction
        $vodacomTransaction = factory(\App\VodacomTransaction::class)->create();
        $transaction = factory(\App\Transaction::class)->make();
        $transaction->amount = 500;
        $vodacomTransaction->transaction()->save($transaction);

        $transactionContainer = TransactionDataContainer::initialize($transaction);

        //with 100 access rate
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 1000;
        $accessRatePayment->save();

        $result = AccessRate::payAccessRate($transactionContainer);

        //get access rate payment again
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();

        $this->assertEquals($result, 0);
        $this->assertEquals($accessRatePayment->debt, 500);
    }

    public function testWithExactlyAccessRateDebt()
    {
        $this->initApplication();
        //fake Transaction
        $vodacomTransaction = factory(\App\VodacomTransaction::class)->create();
        $transaction = factory(\App\Transaction::class)->make();
        $transaction->amount = 1000;
        $vodacomTransaction->transaction()->save($transaction);

        $transactionContainer = TransactionDataContainer::initialize($transaction);

        //with 100 access rate
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();
        $accessRatePayment->debt = 1000;
        $accessRatePayment->save();

        $result = AccessRate::payAccessRate($transactionContainer);

        //get access rate payment again
        $accessRatePayment = $transactionContainer->meter->accessRatePayment()->first();

        $this->assertEquals($result, 0);
        $this->assertEquals($accessRatePayment->debt, 0);
    }

}
