<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 31.07.18
 * Time: 22:03
 */

namespace App\Observers;

use App\Models\Meter\MeterParameter;

class MeterParameterObserver
{
    /**
     * Handle "deleted" event
     * @param MeterParameter $meterParameter
     * @return void
     */
    public function deleted(MeterParameter $meterParameter): void
    {
        // set the meter free
        $meter = $meterParameter->meter()->first();
        $meter->in_use = 0;
        $meter->save();
    }
}
