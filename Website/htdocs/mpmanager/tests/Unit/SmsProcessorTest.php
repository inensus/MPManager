<?php

namespace Tests\Unit;


use App\Jobs\SmsProcessor;
use App\Jobs\TokenProcessor;
use App\Misc\TransactionDataContainer;
use App\Models\Address\Address;
use App\Models\City;
use App\Models\Cluster;
use App\Models\MainSettings;
use App\Models\Manufacturer;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterTariff;
use App\Models\Meter\MeterType;

use App\Models\MiniGrid;
use App\Models\Person\Person;
use App\Models\SmsBody;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;

use App\Sms\Senders\SmsConfigs;
use App\Sms\SmsTypes;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;


class SmsProcessorTest extends TestCase
{
    /*    ./vendor/bin/phpunit --filter   sms_sending_with_transaction     */
    use RefreshDatabase;

    /** @test */
    public function token_creation_with_valid_transaction()
    {
        Queue::fake();
        $transaction = $this->initializeData();
        $transactionContainer = TransactionDataContainer::initialize($transaction);
        $transactionContainer->chargedEnergy = 1;

        TokenProcessor::dispatch(
            $transactionContainer
        );
        Queue::assertPushed(TokenProcessor::class);
    }

    /** @test */
    public function sms_sending_with_transaction()
    {
        Queue::fake();
        $transaction = $this->initializeData();
        $transaction->sender = '905494322161';
        //create sms-bodies
        $this->addSmsBodies();
        SmsProcessor::dispatch(
            $transaction,
            SmsTypes::TRANSACTION_CONFIRMATION,
            SmsConfigs::class
        );
        Queue::assertPushed(SmsProcessor::class);
    }

    /** @test */
    public function sms_sending_with_resend_information_with_no_transaction()
    {
        Queue::fake();
        $transaction = $this->initializeData();
        $transaction->sender = '905494322161';
        //create sms-bodies
        $this->addSmsBodies();

        $data = [
            'phone' => '905494322161',
            'meter' => $transaction->message
        ];
        SmsProcessor::dispatch(
            $data,
            SmsTypes::RESEND_INFORMATION,
            SmsConfigs::class
        );
        Queue::assertPushed(SmsProcessor::class);
    }

    private function addSmsBodies()
    {
        $bodies = [
            [
                'reference' => 'SmsTransactionHeader',
                'place_holder' => 'Dear [name] [surname], we received your transaction [transaction_amount].',
                'variables' => 'name,surname,transaction_amount',
                'title' => 'Sms Header'
            ],
            [
                'reference' => 'SmsReminderHeader',
                'place_holder' => 'Dear [name] [surname],',
                'variables' => 'name,surname',
                'title' => 'Sms Header'
            ],
            [
                'reference' => 'SmsResendInformationHeader',
                'place_holder' => 'Dear [name] [surname], we received your resend last transaction information demand.',
                'variables' => 'name,surname',
                'title' => 'Sms Header'
            ],
            [
                'reference' => 'EnergyConfirmation',
                'place_holder' => 'Meter: [meter] , [token]  Unit [energy] .',
                'variables' => 'meter,token,energy',
                'title' => 'Meter Charge'
            ],
            [
                'reference' => 'AccessRateConfirmation',
                'place_holder' => 'Service Charge: [amount] ',
                'variables' => 'amount',
                'title' => 'Tariff Fixed Cost'
            ],
            [
                'reference' => 'AssetRateReminder',
                'place_holder' => 'the next rate of  [appliance_type_name] ( . [remaining] . ) is due on [due_date]',
                'variables' => 'appliance_type_name,remaining,due_date',
                'title' => 'Appliance Payment Reminder'

            ],
            [
                'reference' => 'AssetRatePayment',
                'place_holder' => 'Appliance:   [appliance_type_name]  [amount]',
                'variables' => 'appliance_type_name,amount',
                'title' => 'Appliance Payment'
            ],
            [
                'reference' => 'OverdueAssetRateReminder',
                'place_holder' => 'you forgot to pay the rate of [appliance_type_name] ( [remaining] )  on [due_date]. Please pay it as soon as possible, unless you wont be able to buy energy.',
                'variables' => 'appliance_type_name,remaining,due_date',
                'title' => 'Overdue Appliance Payment Reminder'
            ],
            [
                'reference' => 'PricingDetails',
                'place_holder' => 'Transaction amount is [amount], \n VAT for energy : [vat_energy] \n VAT for the other staffs : [vat_others] . ',
                'variables' => 'amount,vat_energy,vat_others',
                'title' => 'Pricing Details'
            ],
            [
                'reference' => 'ResendInformation',
                'place_holder' => 'Meter: [meter] , [token]  Unit [energy] KWH. Service Charge: [amount]',
                'variables' => 'meter,token,energy,amount',
                'title' => 'Resend Last Transaction Information'
            ],
            [
                'reference' => 'ResendInformationLastTransactionNotFound',
                'place_holder' => 'Last transaction information not found for Meter: [meter]',
                'variables' => 'meter',
                'title' => 'Last Transaction Information Not Found'
            ],
            [
                'reference' => 'SmsReminderFooter',
                'place_holder' => 'Your Company etc.',
                'variables' => '',
                'title' => 'Sms Footer'
            ],
            [
                'reference' => 'SmsTransactionFooter',
                'place_holder' => 'Your Company etc.',
                'variables' => '',
                'title' => 'Sms Footer'
            ],
            [
                'reference' => 'SmsResendInformationFooter',
                'place_holder' => 'Your Company etc.',
                'variables' => '',
                'title' => 'Sms Footer'
            ]
        ];
        foreach ($bodies as $body) {
            SmsBody::query()->create([
                'reference' => $body['reference'],
                'place_holder' => $body['place_holder'],
                'body' => $body['place_holder'],
                'variables' => $body['variables'],
                'title' => $body['title']
            ]);
        }
        return SmsBody::query()->get();
    }

    private function initializeData()
    {
        //create Main Settings
        factory(MainSettings::class)->create();

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

        //associate address with a person
        $address = Address::query()->make([
            'phone' => '905494322161',
        ]);
        $address->owner()->associate($p);

        //create transaction
        factory(VodacomTransaction::class)->create();
        $transaction = factory(Transaction::class)->make();
        $transaction->message = '4700005646';

        $vodacomTransaction = VodacomTransaction::query()->first();
        $vodacomTransaction->transaction()->save($transaction);

        return $transaction;
    }

}
