<?php

namespace App\Jobs;

use App\Misc\TransactionDataContainer;
use App\Models\Transaction\Transaction;
use App\PaymentHandler\AccessRate;
use App\Sms\SmsTypes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use function config;

class EnergyTransactionProcessor implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $transaction;

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
            $transactionData = TransactionDataContainer::initialize($this->transaction);
        } catch (Exception $e) {
            event('transaction.failed', [$this->transaction, $e->getMessage()]);
            return;
        }


        $loanContainer = resolve('LoanDataContainerProvider');

        $loanContainer->initialize($transactionData->transaction);

        $transactionData->transaction->amount = $loanContainer->loanCost();
        if (empty($loanContainer->paid_rates)) {
            $transactionData->paid_rates = $loanContainer->paid_rates;
        }
        if ($transactionData->transaction->amount > 0) {
            // pay if necessary access rate
            $transactionData->accessRateDebt = AccessRate::payAccessRate($transactionData);
            $transactionData->transaction->amount -= $transactionData->accessRateDebt;
        }

        if ($transactionData->transaction->amount > 0) {
            //give transaction to token processor
            TokenProcessor::dispatch($transactionData)->allOnConnection('redis')->onQueue(config('services.queues.token'));
        } else {
            event('transaction.successful', [$transactionData->transaction]);
            SmsProcessor::dispatch($transactionData,
                SmsTypes::ACCESS_RATE_PAYMENT)->allOnConnection('redis')->onQueue(config('services.queues.sms'));
        }


    }
}
