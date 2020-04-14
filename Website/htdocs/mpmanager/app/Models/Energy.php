<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Energy extends Model
{
    protected $fillable = [
        'meter_id',
        'mini_grid_id',
        'node_id',
        'device_id',
        'total_energy',
        'read_out',
        'used_energy_since_last',
        'active'];
}
