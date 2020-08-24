<?php

namespace App\Listeners;

use App\Lib\ITransactionProvider;

use App\Models\Transaction\Transaction;
use App\Transaction\TransactionAdapter;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use function config;

class TransactionListener
{
    public function onTransactionSaved(ITransactionProvider $transactionProvider): void
    {
        // echos the confirmation output
        $transactionProvider->confirm();
    }

    public function onTransactionFailed(Transaction $transaction, $message = null)
    {
        $baseTransaction = TransactionAdapter::getTransaction($transaction->originalTransaction()->first());
        $baseTransaction->addConflict($message);
        if (config('app.debug')) {
            Log::debug('Transaction failed');
        }
        $baseTransaction->sendResult(false, $transaction);


    }

    public function onTransactionSuccess(Transaction $transaction): void
    {
        $baseTransaction = TransactionAdapter::getTransaction($transaction->originalTransaction()->first());
        $baseTransaction->sendResult(true, $transaction);
    }


    public function subscribe(Dispatcher $events): void
    {
        $events->listen('transaction.saved', 'App\Listeners\TransactionListener@onTransactionSaved');
        $events->listen('transaction.successful', 'App\Listeners\TransactionListener@onTransactionSuccess');
        $events->listen('transaction.failed', 'App\Listeners\TransactionListener@onTransactionFailed');
    }
}
