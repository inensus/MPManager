<?php


namespace App\Services;


use App\Models\AgentBalanceHistory;

class AgentBalanceHistoryService
{

    public function create($agentId, $data)
    {
        return AgentBalanceHistory::query()->create([

            'agent_id' => $agentId,
            'amount' => $data['amount'],
            'type'=>'Charge'
        ]);
    }
}
