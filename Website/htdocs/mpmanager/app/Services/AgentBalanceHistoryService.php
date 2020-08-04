<?php


namespace App\Services;


use App\Models\AgentAssignedAppliances;
use App\Models\AgentBalanceHistory;
use App\Models\AgentCharge;
use App\Models\AgentCommission;
use App\Models\AgentReceipt;
use App\Models\Transaction\AgentTransaction;

class AgentBalanceHistoryService
{

    public function agentBalanceHistories($agentId)
    {


        return AgentBalanceHistory::with(['triggerCharge'])
            ->where('agent_id', $agentId)
            ->whereHasMorph('trigger',
                [
                    AgentCharge::class,
                    AgentCommission::class,
                    AgentTransaction::class,
                    AgentAssignedAppliances::class,
                    AgentReceipt::class
                ])->latest()->paginate();

    }

    public function create($agentId, $data)
    {
        return AgentBalanceHistory::query()->create([

            'agent_id' => $agentId,
            'amount' => $data['amount'],
            'type' => 'Charge'
        ]);
    }
}
