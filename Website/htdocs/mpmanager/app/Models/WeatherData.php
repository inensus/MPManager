<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    protected $fillable = [
        'solar_id',
        'current_weather_data',
        'forecast_weather_data',
        'record_time',
    ];
}
