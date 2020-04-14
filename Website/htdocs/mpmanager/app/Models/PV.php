<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PV extends Model
{
    protected $fillable = [
        'mini_grid_id',
        'node_id',
        'device_id',
        'daily',
        'daily_unit',
        'total',
        'total_unit',
        'new_generated_energy',
        'new_generated_energy_unit',
    ];
}
