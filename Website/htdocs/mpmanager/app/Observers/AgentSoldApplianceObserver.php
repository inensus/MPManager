<?php


namespace App\Observers;


use App\Models\AgentAssignedAppliances;
use App\Models\AgentBalanceHistory;
use App\Models\AgentSoldAppliance;

class AgentSoldApplianceObserver
{
    private $agentBalanceHistory;

    public function __construct(AgentBalanceHistory $agentBalanceHistory)
    {
        $this->agentBalanceHistory = $agentBalanceHistory;

    }

    public function created(AgentSoldAppliance $appliances)
    {
         $assignedApplianceId= $appliances->agent_assigned_appliance_id;
         $assignedAppliance = AgentAssignedAppliances::find($assignedApplianceId);

        AgentBalanceHistory::query()->create([
            'agent_id' => $assignedAppliance->agent_id,
            'amount' => (-1 * $assignedAppliance->cost),
            'type' => 'Appliance'
        ]);
    }
}
