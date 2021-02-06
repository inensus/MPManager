<?php


namespace App\Services;

use App\Models\AgentCommission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AgentCommissionService
{


    public function create(Request $request)
    {
        return AgentCommission::query()->create(
            request()->only(
                'name',
                'energy_commission',
                'appliance_commission',
                'risk_balance'
            )
        );
    }

    public function update($agentCommission, $data)
    {

        $agentCommission->name = $data['name'];
        $agentCommission->energy_commission = $data['energy_commission'];
        $agentCommission->appliance_commission = $data['appliance_commission'];
        $agentCommission->risk_balance = $data['risk_balance'];
        $agentCommission->update();
        return AgentCommission::find($agentCommission->id);
    }

    public function delete($agentCommission)
    {
        $agentCommission->delete();
    }
}
