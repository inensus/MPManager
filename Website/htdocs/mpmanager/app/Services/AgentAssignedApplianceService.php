<?php

namespace App\Services;

use App\Models\AgentAssignedAppliances;

class AgentAssignedApplianceService implements IAgentRelatedService
{

    public function list($agentId)
    {
        return AgentAssignedAppliances::with(['user', 'agent', 'applianceType'])
            ->whereHas(
                'agent',
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                }
            )
            ->latest()->paginate();
    }

    public function create($applianceData)
    {

        return AgentAssignedAppliances::query()->create(
            [
            'agent_id' => $applianceData['agent_id'],
            'user_id' => $applianceData['user_id'],
            'appliance_type_id' => $applianceData['appliance_type_id'],
            'cost' => $applianceData['cost'],

            ]
        );
    }
}
