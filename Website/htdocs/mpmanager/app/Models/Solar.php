<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solar extends Model
{
    protected $fillable = [
        'mini_grid_id',
        'node_id',
        'device_id',
        'solar_reading',
        'time_stamp',
        'min',
        'max',
        'average',
        'duration',
        'readings',
        'start_time',
        'end_time',
    ];
}
