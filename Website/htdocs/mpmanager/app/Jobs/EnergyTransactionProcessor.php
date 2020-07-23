<?php

namespace App\Jobs;

use App\Misc\TransactionDataContainer;
use App\Models\Meter\MeterParameter;
use App\Models\Transaction\Transaction;
use App\PaymentHandler\AccessRate;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use function config;

class EnergyTransactionProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $transaction;

    /**
     * @var TransactionDataContainer
     */
    public $transactionData;

    /**
     * Create a new job instance.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //set transaction type to energy
        $this->transaction->type = 'energy';
        $this->transaction->save();

        try {
            //create an object for the token job
            $this->transactionData = TransactionDataContainer::initialize($this->transaction);
        } catch (Exception $e) {
            event('transaction.failed', [$this->transaction, $e->getMessage()]);
            return;
        }

        $loanContainer = resolve('LoanDataContainerProvider');
        $loanContainer->initialize($this->transactionData->transaction);

        $this->transactionData->transaction->amount = $loanContainer->loanCost();
        if (empty($loanContainer->paid_rates)) {
            $this->transactionData->paid_rates = $loanContainer->paid_rates;
        }
        if ($this->transactionData->transaction->amount > 0) {
            // pay if necessary access rate
            $this->transactionData->accessRateDebt = AccessRate::payAccessRate($this->transactionData);
            $this->transactionData->transaction->amount -= $this->transactionData->accessRateDebt;
        }

        if ($this->transactionData->transaction->amount > 0) {
            $this->transactionData->chargedEnergy = $this->calculateChargedEnergy($this->transactionData->meterParameter,
                $this->transactionData->transaction->amount);

            try {
                $api = resolve($this->transactionData->manufacturer->api_name);
            } catch (Exception $e) {
                //no api found
                Log::critical('No Api is registered for ' . $this->transactionData->manufacturer->name,
                    ['id' => '34758734658734567885458923', 'message' => $e->getMessage()]);
                event('transaction.failed', $this->transactionData->transaction);
                return;
            }

            //give transaction to token processor
            TokenProcessor::dispatch(
                $api, $this->transactionData)->allOnConnection('redis')->onQueue(config('services.queues.token'));
        } else {
            event('transaction.successful', [$this->transactionData->transaction]);
            SmsProcessor::dispatch($this->transactionData->transaction,
                SmsTypes::ACCESS_RATE_PAYMENT)->allOnConnection('redis')->onQueue(config('services.queues.sms'));
        }

    }

    private function calculateChargedEnergy(MeterParameter $meterParameter, $amount): float
    {
        $kWhToBeCharged = 0.0;
        // get piggy-bank energy
        try {
            $bankAccount = $meterParameter->socialTariffPiggyBank()->firstOrFail();
            // calculate the cost of savings. To achive that, the price (for kWh.) should converted to Wh. (/1000)
            // the price is x100 in the database to keep the price as integer. The last two digits are decimal parts
            $savingsCost = $bankAccount->savings * (($bankAccount->socialTariff->price / 1000) / 100);
            if ($amount >= $savingsCost) {
                $kWhToBeCharged += $bankAccount->savings / 1000;
                $amount -= $savingsCost;
            } else {
                $amount = 0;
                $kWhToBeCharged += $bankAccount->savings / 1000;
                $bankAccount->savings -= $amount / (($bankAccount->socialTariff->price / 1000) / 100);
            }

            $bankAccount->update();

        } catch (ModelNotFoundException $exception) {
            // meter has no piggy bank account
        }
        $kWhToBeCharged += $amount / ($meterParameter->tariff()->first()->total_price / 100);

        return round($kWhToBeCharged, 2);
    }
}
