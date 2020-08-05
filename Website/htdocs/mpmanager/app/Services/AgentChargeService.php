<?php


namespace App\Services;


use App\Models\AgentBalanceHistory;
use App\Models\AgentCharge;

class AgentChargeService
{

    public function create($agentId, $data)
    {


        $agentCharge = AgentCharge::query()->create([
            'agent_id' => $agentId,
            'user_id' => $data['user_id'],
        ]);

        $history = AgentBalanceHistory::query()->make([
            'agent_id' => $agentId,
            'amount' => request()->input('amount'),
        ]);
        $history->trigger()->associate($agentCharge);
        $history->save();
        return $agentCharge->fresh();

    }
}
