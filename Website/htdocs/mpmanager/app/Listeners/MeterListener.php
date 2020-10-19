<?php

namespace App\Listeners;

use App\Models\Meter\Meter;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;


class MeterListener
{
    /**
     * Sets the in_use to true
     * @param int $meter_id
     */
    public function onParameterSaved(int $meter_id)
    {
        Log::debug('listener Core',[]);
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen('meterparameter.saved', 'App\Listeners\MeterListener@onParameterSaved');
    }

}
