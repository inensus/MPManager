<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends BaseModel
{

    public function agentAssignedAppliance(): HasMany
    {
        return $this->hasMany(AgentAssignedAppliances::class, 'id', 'appliance_type_id');
    }

    public function rates(): HasMany
    {
        return $this->hasMany(AssetPerson::class);
    }
}
