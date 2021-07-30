<?php

namespace App\Listeners;

use App\Events\ClusterMetaDataEvent;
use App\Models\Address\Address;
use App\Models\ClusterMetaData;
use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;
use Illuminate\Events\Dispatcher;

class ClusterMetaDataListener
{
    public function incrementConnectedMetersCount(MeterParameter $meterParameter): void
    {
        $mini_grid_id = $this->getMiniGridId($meterParameter);

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)->first()->increment('connected_meters');
    }

    public function decrementConnectedMetersCount(MeterParameter $meterParameter): void
    {
        $mini_grid_id = $this->getMiniGridId($meterParameter);

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)
            ->first()
            ->decrement('connected_meters');
    }

    public function incrementRegisteredCustomersCount(Person $person): void
    {
        $address = Address::with('city')->where('owner_id', '=', $person->id)->first();

        $mini_grid_id = $address->city->mini_grid_id;

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)->first()->increment('registered_customers');
    }

    public function decrementRegisteredCustomersCount(Person $person): void
    {
        $address = Address::with('city')->where('owner_id', '=', $person->id)->first();

        $mini_grid_id = $address->city->mini_grid_id;

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)->first()->decrement('registered_customers');
    }

    public function getMiniGridId($meterParameter)
    {
        $address = Address::with('city')->where('owner_id', '=', $meterParameter->owner_id)->first();

        return $address->city->mini_grid_id;
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen('cluster_meta.connected_meters.increment',
            '\App\Listeners\ClusterMetaDataListener@incrementConnectedMetersCount');
        $events->listen('cluster_meta.connected_meters.decrement',
            '\App\Listeners\ClusterMetaDataListener@decrementConnectedMetersCount');
        $events->listen('cluster_meta.registered_customers.increment',
            '\App\Listeners\ClusterMetaDataListener@incrementRegisteredCustomersCount');
        $events->listen('cluster_meta.registered_customers.decrement',
            '\App\Listeners\ClusterMetaDataListener@decrementRegisteredCustomersCount');
    }
}
