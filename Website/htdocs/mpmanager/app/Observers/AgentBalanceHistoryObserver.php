<?php


namespace App\Observers;


use App\Models\Agent;
use App\Models\AgentAssignedAppliances;
use App\Models\AgentBalanceHistory;
use App\Models\AgentCommission;

class AgentBalanceHistoryObserver
{
    private $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Handle the asset person "updated" event.
     *
     * @param AgentBalanceHistory $agentBalanceHistory
     * @return void
     */
    public function created(AgentBalanceHistory $agentBalanceHistory)
    {
        $trigger = $agentBalanceHistory->trigger()->first();
        $agent = Agent::query()->find($agentBalanceHistory->agent_id);
        $agent->balance += $agentBalanceHistory->amount;
        $agent->update();
        //
    }
}
