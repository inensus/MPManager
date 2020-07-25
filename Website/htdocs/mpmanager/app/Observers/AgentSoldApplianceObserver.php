<?php


namespace App\Observers;


use App\Models\Agent;
use App\Models\AgentAssignedAppliances;
use App\Models\AgentBalanceHistory;
use App\Models\AgentCommission;
use App\Models\AgentSoldAppliance;
use App\Models\AssetRate;
use App\Models\Person\Person;
use App\Models\Transaction\AgentTransaction;
use App\Models\Transaction\Transaction;

class AgentSoldApplianceObserver
{
    private $agentBalanceHistory;

    public function __construct(AgentBalanceHistory $agentBalanceHistory)
    {
        $this->agentBalanceHistory = $agentBalanceHistory;

    }

    public function created(AgentSoldAppliance $appliances)
    {

        $assignedApplianceId = $appliances->agent_assigned_appliance_id;
        $assignedAppliance = AgentAssignedAppliances::query()->find($assignedApplianceId);

        $appliance = $assignedAppliance->applianceType()->first();
        $agent = Agent::query()->find($assignedAppliance->agent_id);
        $buyer = Person::query()->find(request()->input('person_id'));
        //create agent transaction
        $agentTransaction = AgentTransaction::query()->create([
            'agent_id' => $agent->id,
            'device_id' => $agent->device_id,
            'status' => 1,
        ]);

        $transaction = Transaction::query()->make([
            'amount' => request()->input('down_payment'),
            'sender' => $agent->device_id,
            'message' => '-',
        ]);
        $transaction->originalTransaction()->associate($agentTransaction);
        $transaction->save();

        event('payment.successful', [
            'amount' => $transaction->amount,
            'paymentService' => 'agent',
            'paymentType' => 'appliance',
            'sender' => $agent->device_id,
            'paidFor' => $appliance,
            'payer' => $buyer,
            'transaction' => $transaction,
        ]);

        $history = AgentBalanceHistory::query()->make([
            'agent_id' => $agent->id,
            'amount' => (-1 * request()->input('down_payment')),
            'transaction_id' => $transaction->id,

        ]);
        $history->trigger()->associate($assignedAppliance);
        $history->save();


        //create agent commission
        $commission = AgentCommission::query()->find($agent->agent_commission_id);

        $history = AgentBalanceHistory::query()->make([
            'agent_id' => $agent->id,
            'amount' => ($assignedAppliance->cost * $commission->appliance_commission),
            'transaction_id' => $transaction->id,
        ]);
        $history->trigger()->associate($commission);
        $history->save();


        $tenure = request()->input('tenure');
        $downPayment = request()->input('down_payment');
        $remainingCost = $assignedAppliance->cost - $downPayment;
        $firstPaymentDate = request()->input('first_payment_date');
        $base_time = time();
        AssetRate::query()->create([
            'asset_person_id' => $buyer->id,
            'rate_cost' => $downPayment,
            'remaining' => 0,
            'due_date' =>date("Y-m-d H:i:s")
        ]);


        foreach (range(1, $tenure) as $rate) {
            if ((int)$rate === (int)$tenure) {
                //last rate
                $rate_cost = $remainingCost - (($rate - 1) * floor($remainingCost / $tenure));
            } else {
                $rate_cost = floor($remainingCost / $tenure);
            }
            $rate_date = date('Y-m-d', strtotime('+' . $rate-1 . ' month', strtotime($firstPaymentDate)));
            AssetRate::query()->create([
                'asset_person_id' => $buyer->id,
                'rate_cost' => $rate_cost,
                'remaining' => $rate_cost,
                'due_date' => $rate_date
            ]);
        }
    }
}
