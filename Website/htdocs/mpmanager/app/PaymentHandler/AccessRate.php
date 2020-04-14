<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 02.07.18
 * Time: 10:57
 */

namespace App\PaymentHandler;


use App\Exceptions\AccessRates\NoAccessRateFound;
use App\Jobs\SmsProcessor;
use App\Misc\TransactionDataContainer;
use App\Models\AccessRate\AccessRatePayment;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Sms\SmsTypes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Log;

class AccessRate
{
    /**
     * @var AccessRate
     */
    private $accessRate;
    /**
     * @var MeterParameter
     */
    private $meterParameter;


    /**
     * AccessRatePayment constructor.
     * @param AccessRate $accessRate
     */
    public function __construct()
    {

    }


    /**
     * @param MeterParameter $meterParameter
     * @return AccessRate
     * @throws NoAccessRateFound
     */
    public static function withMeterParameters(MeterParameter $meterParameter): AccessRate
    {
        if ($meterParameter->tariffAccessRate() === null) {
            throw new NoAccessRateFound('Tariff  ' . $meterParameter->tariff()->first()->name . ' has no access rate');
        }
        $accessRate = new self();
        $accessRate->accessRate = $meterParameter->tariffAccessRate();
        $accessRate->setMeterParameter($meterParameter);
        return $accessRate;
    }

    private function setMeterParameter(MeterParameter $meterParameter): void
    {
        $this->meterParameter = $meterParameter;
    }


    /**
     * @return AccessRatePayment
     * @throws NoAccessRateFound
     */
    public function initializeAccessRatePayment(): AccessRatePayment
    {
        if ($this->accessRate === null || $this->meterParameter === null) {
            throw new NoAccessRateFound(
                sprintf('%s %s',
                    $this->accessRate === null ? 'Access Rate is not set' : '',
                    $this->meterParameter === null ? 'Meter Parameter is not set' : '')
            );
        }
        // get current date and add AccessRate.period days
        $nextPaymentDate = Carbon::now()->addDays($this->accessRate->period)->toDateString();
        //create accessRatePayment instance and fill with the variables
        $accessRatePayment = new AccessRatePayment();
        $accessRatePayment->accessRate()->associate($this->accessRate);
        $accessRatePayment->meter()->associate($this->meterParameter->meter()->first());
        $accessRatePayment->due_date = $nextPaymentDate;
        $accessRatePayment->debt = 0;
        return $accessRatePayment;
    }


    /**
     * @return int
     * @throws NoAccessRateFound
     */
    private function getDebt(Meter $meter): int
    {
        $accessRate = $meter->accessRatePayment()->first();
        if ($accessRate === null) {
            throw new NoAccessRateFound('no access rate is defined');
        }

        return $accessRate->debt;
    }

    /**
     * @param TransactionDataContainer $transactionData
     * @return int
     */
    public static function payAccessRate(TransactionDataContainer $transactionData): int
    {
        $nonStaticGateway = new self;
        //get accessRatePayment
        $accessRatePayment = $nonStaticGateway->getAccessRatePayment($transactionData->meter);
        try {
            $debt_amount = $nonStaticGateway->getDebt($transactionData->meter);
        } catch (NoAccessRateFound $e) { //no access rate found
            return 0;
        }

        if ($debt_amount > 0) { //there is unpaid amount
            $satisfied = true;
            if ($debt_amount > $transactionData->transaction->amount) { //sent amount doesn't cover the debt. Spend all money for the debt.
                $debt_amount = $transactionData->transaction->amount;
                $transactionData->transaction->amount = 0;
                $satisfied = false;
            } else {
                //$transactionData->transaction->amount -= $debt_amount;
            }
            $nonStaticGateway->updatePayment($accessRatePayment, $debt_amount, $satisfied);

            //add payment history for the client
            event('payment.successful', [
                'amount' => $debt_amount,
                'paymentService' =>  $transactionData->transaction->original_transaction_type, //$tokenData->transaction->owner_type,
                'paymentType' => 'access rate',
                'sender' => $transactionData->transaction->sender,
                'paidFor' => $transactionData->meter->accessRate(),
                'payer' => $transactionData->meterParameter->owner,
                'transaction' => $transactionData->transaction,
            ]);
        }
        return $debt_amount ?? 0;
    }


    public function updatePayment($accessRatePayment, int $paidAmount, bool $satisfied = false)
    {
        $accessRatePayment->debt = $satisfied === true ? 0 : $accessRatePayment->debt - $paidAmount;
        $accessRatePayment->save();
    }


    private function getAccessRatePayment(Meter $meter)
    {
        return $meter->accessRatePayment()->first();
    }
}
