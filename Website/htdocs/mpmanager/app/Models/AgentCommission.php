<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentCommission extends BaseModel
{

    public function agent()
    {
        return $this->belongsTo(Agent::Class);
  }
}
