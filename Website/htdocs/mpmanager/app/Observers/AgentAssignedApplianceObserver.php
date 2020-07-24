<?php


namespace App\Observers;


use App\Models\AgentAssignedAppliances;
use App\Models\AgentBalanceHistory;

class AgentAssignedApplianceObserver
{
    private $agentBalanceHistory;

    public function __construct(AgentBalanceHistory $agentBalanceHistory)
    {
        $this->agentBalanceHistory = $agentBalanceHistory;

    }

    public function created(AgentAssignedAppliances $appliances)
    {
        AgentBalanceHistory::query()->create([
            'agent_id' => $appliances->agent_id,
            'amount' => (-1 * $appliances->cost),
            'type' => 'Appliance'
        ]);
    }
}
