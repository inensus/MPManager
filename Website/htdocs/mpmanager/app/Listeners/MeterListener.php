<?php

namespace App\Listeners;

use App\Models\Meter\Meter;
use Illuminate\Events\Dispatcher;


class MeterListener
{
    /**
     * Sets the in_use to true
     * @param int $meter_id
     */
    public function onParameterSaved(int $meter_id)
    {
        $meter = Meter::find($meter_id);
        $meter->in_use = 1;
        $meter->save();
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen('meterparameter.saved', 'App\Listeners\MeterListener@onParameterSaved');
    }

}
