<?php


namespace App\Observers;


use App\Models\Agent;
use App\Models\AgentBalanceHistory;
use App\Models\Transaction\AgentTransaction;
use App\Models\Transaction\Transaction;

class TransactionObserver
{
    private $agentBalanceHistory;

    public function __construct(AgentBalanceHistory $agentBalanceHistory)
    {
        $this->agentBalanceHistory = $agentBalanceHistory;
    }

    public function created(Transaction $transaction)
    {

        if ($transaction->original_transaction_type !== 'vodacom_transaction' && $transaction->original_transaction_type !== 'airtel_transaction') {
            $agentTransaction = AgentTransaction::query()->where('id',
                $transaction->original_transaction_id)->first();
            AgentBalanceHistory::query()->create([
                'agent_id' => $agentTransaction->agent_id,
                'amount' => (-1 * floatval($transaction->amount)),

            ]);
        }

    }
}
