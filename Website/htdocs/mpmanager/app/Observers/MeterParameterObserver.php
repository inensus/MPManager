<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 31.07.18
 * Time: 22:03
 */

namespace App\Observers;

use App\Events\ClusterMetaDataEvent;
use App\Jobs\CreatePiggyBankEntry;
use App\Jobs\UpdatePiggyBankEntry;
use App\Models\Meter\Meter;
use App\Models\Meter\MeterParameter;

class MeterParameterObserver
{
    /**
     * Handle the MeterParameter "deleted" event
     *
     * @param MeterParameter $meterParameter
     * @return void
     */
    public function deleted(MeterParameter $meterParameter)
    {
        // set the meter free
        $meter = $meterParameter->meter()->first();
        $meter->in_use = 0;
        $meter->save();

        event('cluster_meta.connected_meters.decrement', $meterParameter);
    }

    /**
     * Handle the MeterParameter "created" event
     *
     * @param MeterParameter $meterParameter
     * @return void
     */
    public function created(MeterParameter $meterParameter): void
    {
        CreatePiggyBankEntry::dispatchSync($meterParameter);
        $meter = Meter::find($meterParameter->meter_id);
        $meter->in_use = 1;
        $meter->save();

        event('cluster_meta.connected_meters.increment', $meterParameter);

    }

    /**
     * Handle the MeterParameter "updated" event
     *
     * @param MeterParameter $meterParameter
     * @return void
     */
    public function updated(MeterParameter $meterParameter): void
    {
        UpdatePiggyBankEntry::dispatchSync($meterParameter);
    }
}
