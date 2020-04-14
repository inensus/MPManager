<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 19:22
 */

namespace App\Http\Services;


use App\Http\Middleware\Transaction;
use App\Models\Meter\Meter;
use Illuminate\Database\Eloquent\Collection;

class TransactionService
{
    /**
     * @var Transaction
     */
    private $transaction;
    /**
     * @var Meter
     */
    private $meter;

    /**
     * TransactionService constructor.
     *
     * @param Transaction $transaction
     * @param Meter $meter
     */
    public function __construct(Transaction $transaction, Meter $meter)
    {
        $this->transaction = $transaction;
        $this->meter = $meter;
    }

    /**
     * @param $meters Collection :Model Collection
     *
     * @param $range
     *
     * @return int
     */
    public function totalMeterTransactions(Collection $meters, $range): int
    {
        $total = $this->meter->sumOfTransactions($meters->pluck('serial_number')->toArray(), $range);
        return $total[0]['total'] ?? 0;
    }

}
