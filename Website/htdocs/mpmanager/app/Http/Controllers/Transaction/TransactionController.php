<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 02.08.18
 * Time: 16:42
 */

namespace App\Http\Controllers\Transaction;

use App\Http\Resources\ApiResource;
use App\Models\Transaction\Transaction;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Transaction
 *
 * @group Transaction
 */
class TransactionController
{
    private $transaction;


    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * A list of transactions
     * @return Factory|View
     */
    public function index()
    {
        $transactions = $this->transaction::with('originalTransaction')->get();
        //return new ApiResource($transactions);
        $title = 'incomming Transactions';
        return view('layouts.transactions.list', compact('transactions', 'title'));
    }

    /**
     * Detailed information about given transaction id
     *
     * @param int $id
     * @return Factory|View
     */
    public function show(int $id)
    {
        $title = '#' . $id;
        //todo: add deferred_payment relation after it implemented
        $transaction = Transaction::with('token', 'originalTransaction', 'sms', 'token.meter',
            'token.meter.meterParameter', 'token.meter.meterType')->where('id', $id)->first();
        return view('layouts.transactions.detail', compact('transaction', 'title'));
    }
}
