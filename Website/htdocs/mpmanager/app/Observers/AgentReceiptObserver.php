<?php


namespace App\Observers;


use App\Models\Agent;
use App\Models\AgentBalanceHistory;
use App\Models\AgentReceipt;

class AgentReceiptObserver
{
    private $agentBalanceHistory;

    public function __construct(AgentBalanceHistory $agentBalanceHistory)
    {
        $this->agentBalanceHistory = $agentBalanceHistory;

    }

    public function created(AgentReceipt $receipt)
    {
        $agent = Agent::query()->find($receipt->agent_id);
        $history = AgentBalanceHistory::query()->make([
            'agent_id' => $agent->id,
            'amount' => $receipt->amount,
            'transaction_id' => $receipt->id,

        ]);
        $history->trigger()->associate($receipt);
        $history->save();

    }
}
