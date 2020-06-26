<?php

namespace App\Models;

use App\Models\Meter\MeterParameter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ConnectionGroup
 *
 * @package App\Models
 */
class ConnectionGroup extends Model
{

    public function meterParameters(): HasMany
    {
        return $this->hasMany(MeterParameter::class);
    }

    public function meterParametersCount($till)
    {
        return $this->meterParameters()
            ->selectRaw('connection_group_id, count(*) as aggregate')
            ->where('created_at', '<=', $till)
            ->groupBy('connection_group_id');
    }

}
