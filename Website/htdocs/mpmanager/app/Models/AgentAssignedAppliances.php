<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentAssignedAppliances extends Model
{
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function applianceType()
    {
        return $this->belongsTo(AssetType::class, 'appliance_type_id', 'id');
    }

    public function soldAppliance()
    {
        return $this->hasMany(AgentSoldAppliance::class);
    }
}
