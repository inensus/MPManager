<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.06.18
 * Time: 11:13
 */

namespace App\Transaction;

use App\Lib\ITransactionProvider;
use App\Models\Transaction\AgentTransaction;
use App\Models\Transaction\AirtelTransaction;
use App\Models\Transaction\VodacomTransaction;

class TransactionAdapter
{
    /**
     * @param ITransactionProvider $transactionProvider
     *
     * @return ITransactionProvider
     */
    public static function getTransaction(ITransactionProvider $transactionProvider): ?ITransactionProvider
    {
        if ($transactionProvider instanceof VodacomTransaction) {
            $baseTransaction = resolve('VodacomPaymentProvider');
            $baseTransaction->init($transactionProvider);
            return $baseTransaction;
        }

        if ($transactionProvider instanceof AirtelTransaction) {
            $baseTransaction = resolve('AirtelPaymentProvider');
            $baseTransaction->init($transactionProvider);
            return $baseTransaction;
        }
        if ($transactionProvider instanceof AgentTransaction) {
            $baseTransaction = resolve('AgentPaymentProvider');
            $baseTransaction->init($transactionProvider);
            return $baseTransaction;
        }
        return null;
    }
}
