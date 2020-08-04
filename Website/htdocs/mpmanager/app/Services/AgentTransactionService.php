<?php


namespace App\Services;


use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Transaction\AgentTransaction;
use App\Models\Transaction\Transaction;

class AgentTransactionService implements IAgentRelatedService
{

    public function list($agentId)
    {
        return Transaction::with('originalAgent')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                static function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->latest()->paginate();
    }

    public function listByCustomer($agentId, $customerId)
    {
        $customerMeters = MeterParameter::query()->select('meter_id')->where('owner_id', $customerId)->get();
        if ($customerMeters->count() === 0) {
            // customer has no meters
            return null;
        }


        $customerMeterId = $customerMeters->id;
        $customerMeterSerialNumbers = Meter::query()
            ->has('meterParameter')
            ->whereHas('meterParameter', static function ($q) use ($customerMeterId) {
                $q->where('id', $customerMeterId);
            })->get('serial_number');

        return Transaction::with('originalAgent')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                static function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->whereHas('meter', static function ($q) use ($customerMeterSerialNumbers) {
                $q->whereIn('serial_number', $customerMeterSerialNumbers);

            })
            ->latest()->paginate();
    }


}
