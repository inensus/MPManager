<?php


namespace App\Observers;


use App\Models\Agent;
use App\Models\AgentBalanceHistory;

class AgentBalanceHistoryObserver
{
    private $agent;
    public function __construct(Agent $agent)
    {
        $this->agent=$agent;
    }

    /**
     * Handle the asset person "updated" event.
     *
     * @param AgentBalanceHistory $agentBalanceHistory
     * @return void
     */
    public function created(AgentBalanceHistory $agentBalanceHistory)
    {
        $agent = Agent::query()->find($agentBalanceHistory->agent_id);
        $agent->balance += $agentBalanceHistory->amount;
        $agent->update();
        //
    }
}
