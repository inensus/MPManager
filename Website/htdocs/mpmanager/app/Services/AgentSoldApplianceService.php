<?php


namespace App\Services;

use App\Models\Agent;
use App\Models\AgentSoldAppliance;
use App\Models\AssetPerson;

class AgentSoldApplianceService implements IAgentRelatedService
{

    public function list($agentId)
    {
        return AssetPerson::with('person', 'assetType', 'rates')
            ->whereHasMorph('creator', [Agent::class],
                function ($q) use ($agentId) {
                    $q->where('id', $agentId);
                })->latest()
            ->paginate();


    }

    public function customerSoldList($customerId, $agentId)
    {
        return AssetPerson::with('person', 'assetType', 'rates')
            ->whereHasMorph('creator', [Agent::class],
                function ($q) use ($agentId) {
                    $q->where('id', $agentId);
                })
            ->where('person_id', $customerId)
            ->latest()
            ->paginate();
    }

    public function create($applianceData)
    {
        return AgentSoldAppliance::query()->create([

            'person_id' => $applianceData['person_id'],
            'agent_assigned_appliance_id' => $applianceData['agent_assigned_appliance_id'],
        ]);
    }

    public function listForWeb($agentId)
    {

        return AgentSoldAppliance::with([
            'assignedAppliance',
            'assignedAppliance.applianceType',
            'person',

        ])
            ->whereHas('assignedAppliance', function ($q) use ($agentId) {
                $q->whereHas('agent', function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                }
                );

            })->latest()->paginate();

    }
}
