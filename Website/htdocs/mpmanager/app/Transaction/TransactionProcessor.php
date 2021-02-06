<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 25.05.18
 * Time: 15:08
 */

namespace App\Transaction;

use App\Customer;
use App\PaymentHistory;
use App\Transaction;

class TransactionProcessor
{

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var Customer
     */
    private $client;

    /**
     * @var PaymentHistory
     */
    private $clientPaymentHistory;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    private function getClient()
    {
        $this->client = Customer::where('phone', $this->transaction->sender)->first();
    }

    private function getTariff()
    {
        $this->client->meter()->where('meter_id', $this->transaction->message);
    }

    private function getClientPaymentHistory()
    {
    }
}
