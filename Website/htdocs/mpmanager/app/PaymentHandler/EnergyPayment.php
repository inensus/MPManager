<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 02.07.18
 * Time: 10:49
 */

namespace App\PaymentHandler;

use App\Transaction;

class EnergyPayment
{
    /**
     * EnergyPayment constructor.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
    }

    private function checkAccessRate()
    {
    }
}
