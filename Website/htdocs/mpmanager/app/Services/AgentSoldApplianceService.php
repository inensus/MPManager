<?php


namespace App\Services;

use App\Models\AgentSoldAppliance;

class AgentSoldApplianceService implements IAgentRelatedService
{

    public function list($agentId)
    {

        return AgentSoldAppliance::with([
            'assignedAppliance',
            'person'
        ])
            ->whereHas('assignedAppliance', function ($q) use ($agentId) {
                $q->whereHas('agent', function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                });

            })->latest()->paginate();

    }

    public function customerSoldList($customerId, $agentId)
    {
        return AgentSoldAppliance::with([
            'assignedAppliance',
        ])->where('person_id', $customerId)
            ->whereHas('assignedAppliance', function ($q) use ($agentId) {
                $q->whereHas('agent', function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                });
            })->latest()->paginate();
    }

    public function create($applianceData)
    {
        return AgentSoldAppliance::query()->create([

            'person_id' => $applianceData['person_id'],
            'agent_assigned_appliance_id' => $applianceData['agent_assigned_appliance_id'],
        ]);
    }
}
