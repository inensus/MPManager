<?php


namespace App\Services;


use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;
use App\Models\Transaction\AgentTransaction;
use App\Models\Transaction\AirtelTransaction;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\VodacomTransaction;

class AgentTransactionService implements IAgentRelatedService
{

    public function list($agentId)
    {
        $transactions = Transaction::with('originalAgent')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->latest()->paginate();


        return $transactions;

    }

    public function listByCustomer($agentId, $customerId)
    {
        $customerMeters = MeterParameter::where('owner_id', $customerId)->firstOrFail();

        $cutomerMeterId = $customerMeters->id;
        $customerMeterSerialNumbers = Meter::has('meterParameter')
            ->whereHas('meterParameter', static function ($q) use ($cutomerMeterId) {
                $q->where('id', $cutomerMeterId);

            })->get('serial_number');

        $customerTransactions = Transaction::with('originalAgent')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->whereHas('meter', static function ($q) use ($customerMeterSerialNumbers) {
                $q->whereIn('serial_number', $customerMeterSerialNumbers);

            })
            ->latest()->paginate();
        return $customerTransactions;
    }


    public function listForWeb($agentId)
    {
        $transactions = Transaction::with( 'meter.meterParameter.owner')
            ->where('type', 'energy')
            ->whereHasMorph('originalTransaction', [AgentTransaction::class],
                function ($q) use ($agentId) {
                    $q->where('agent_id', $agentId);
                })
            ->latest()->paginate();


        return $transactions;

    }
}
