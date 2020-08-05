<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.06.18
 * Time: 11:13
 */

namespace App\Transaction;


use App\Lib\ITransactionProvider;
use App\Models\Transaction\VodacomTransaction;
use App\Models\Transaction\AirtelTransaction;

class TransactionAdapter
{
    /**
     * @param $transactionProvider
     * @return ITransactionProvider
     */
    public static function getTransaction($transactionProvider): ?ITransactionProvider
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
        return null;
    }
}
