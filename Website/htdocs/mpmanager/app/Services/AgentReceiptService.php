<?php


namespace App\Services;


use App\Models\AgentReceipt;
use App\Models\AgentBalanceHistory;
use Matrix\Exception;

class AgentReceiptService implements IAgentRelatedService
{
    public function list($agentId)
    {
        return AgentReceipt::with(['user', 'agent', 'history'])
            ->whereHas('agent',
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->latest()->paginate();

    }

    public function listAllReceipts()
    {
        return AgentReceipt::with(['user', 'agent', 'history'])->latest()->paginate();

    }

    public function listReceiptsForUser($userId)
    {
        return AgentReceipt::with(['user', 'agent', 'history'])
            ->whereHas('user',
                function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
            ->latest()->paginate();

    }

    public function create($userId, $receiptData)
    {

        $lastBalanceHistoryId = AgentBalanceHistory::where('agent_id', $receiptData['agent_id'])->get()->last();

        return AgentReceipt::query()->create([
            'agent_id' => $receiptData['agent_id'],
            'user_id' => $userId,
            'amount' => $receiptData['amount'],
            'last_controlled_balance_history_id' => $lastBalanceHistoryId->id
        ]);


    }
}
