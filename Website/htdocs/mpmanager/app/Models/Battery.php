<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battery extends Model
{
    protected $fillable = [
        'mini_grid_id',
        'battery_id',
        'node_id',
        'device_id',
        'read_out',
        'battery_count',
        'soc_average',
        'soc_unit',
        'soc_min',
        'soc_max',
        'soh_average',
        'soh_unit',
        'soh_min',
        'soh_max',
        'd_total',
        'd_total_unit',
        'd_newly_energy',
        'd_newly_energy_unit',
    ];
}
