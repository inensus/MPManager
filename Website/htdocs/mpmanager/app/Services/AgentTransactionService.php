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
        return Transaction::with(['originalAgent', 'meter.meterParameter.owner'])
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

        $meterIds = array();
        foreach ($customerMeters as $key => $item) {
            array_push($meterIds, $item->meter_id);
        }


        $customerMeterSerialNumbers = Meter::query()
            ->has('meterParameter')
            ->whereHas('meterParameter', static function ($q) use ($meterIds) {
                $q->whereIn('meter_id', $meterIds);
            })->get('serial_number');


        return Transaction::with(['originalAgent', 'meter.meterParameter.owner'])
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                static function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->whereHas('meter', static function ($q) use ($customerMeterSerialNumbers) {
                $q->whereIn('serial_number', $customerMeterSerialNumbers);

            })
            ->latest()->paginate();
    }


    public function listForWeb($agentId)
    {
        $transactions = Transaction::with('meter.meterParameter.owner')
            ->where('type', 'energy')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->latest()->paginate();


        return $transactions;

    }
}
