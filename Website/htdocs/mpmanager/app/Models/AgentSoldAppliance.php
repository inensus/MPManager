<?php

namespace App\Models;

use App\Models\Person\Person;
use Illuminate\Database\Eloquent\Model;

class AgentSoldAppliance extends Model
{
    protected $guarded = [];

    public function assignedAppliance()
    {
        return $this->belongsTo(AgentAssignedAppliances::class, 'agent_assigned_appliance_id', 'id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
