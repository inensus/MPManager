<?php

namespace App\Jobs;

use App\Misc\TransactionDataContainer;
use App\Models\Transaction\Transaction;
use App\PaymentHandler\AccessRate;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use function config;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Transaction
     */
    protected $transactionID;

    /**
     * Create a new job instance.
     *
     * @param int $transaction_id
     */
    public function __construct(int $transaction_id)
    {
        $this->transactionID = $transaction_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $transaction = Transaction::find($this->transactionID);
        EnergyTransactionProcessor::dispatch($transaction)
            ->allOnConnection('redis')
            ->onQueue(config('services.queues.energy'));
    }


    /**
     * Entry point for Loan Payments
     *
     * @param Transaction $transaction
     */
    private function handleLoanPayment(Transaction $transaction): void
    {
    }

    /**
     * Entry point for Energy Payments
     *
     * @param Transaction $transaction
     */
    private function handleEnergyPayment(Transaction $transaction): void
    {
        try {
            //create an object for the token job
            $transactionData = TransactionDataContainer::initialize($transaction);
        } catch (Exception $e) {
            event('transaction.failed', [$transaction, $e->getMessage()]);
            return;
        }

        // pay if necessary access rate
        $transactionData->transaction->amount = AccessRate::payAccessRate($transactionData);

        event('token.generate', [$transactionData]);
        // if token success send sms

        //fire transaction successful event.
    }
}
