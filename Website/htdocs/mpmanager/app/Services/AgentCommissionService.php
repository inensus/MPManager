<?php


namespace App\Services;

use App\Models\AgentCommission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AgentCommissionService
{


    /**
     * @return Model|Builder
     */
    public function create()
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

    /**
     * @param AgentCommission $agentCommission
     * @param array $data
     * @return mixed
     */
    public function update(AgentCommission $agentCommission, array $data)
    {
        $agentCommission->name = $data['name'];
        $agentCommission->energy_commission = $data['energy_commission'];
        $agentCommission->appliance_commission = $data['appliance_commission'];
        $agentCommission->risk_balance = $data['risk_balance'];
        $agentCommission->update();
        return AgentCommission::find($agentCommission->id);
    }

    /**
     * @param AgentCommission $agentCommission
     * @throws \Exception
     */
    public function delete(AgentCommission $agentCommission): void
    {
        $agentCommission->delete();
    }
}
