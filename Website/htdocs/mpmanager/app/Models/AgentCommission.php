<?php

namespace App\Models;

/**
 * Class AgentCommission
 *
 * @package  App\Models
 * @property int $id
 * @property double $energy_commission
 * @property double $appliance_commission
 * @property double $risk_balance
 */
class AgentCommission extends BaseModel
{

    public function agent()
    {
        return $this->hasMany(Agent::Class);
    }
}
