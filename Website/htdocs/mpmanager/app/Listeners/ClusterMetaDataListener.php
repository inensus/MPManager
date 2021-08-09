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
    public function increaseConnectedMetersCount(MeterParameter $meterParameter): void
    {
        $mini_grid_id = $this->getMiniGridId($meterParameter);

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)->first()->increment('connected_meters');
    }

    public function decreaseConnectedMetersCount(MeterParameter $meterParameter): void
    {
        $mini_grid_id = $this->getMiniGridId($meterParameter);

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)
            ->first()
            ->decrement('connected_meters');
    }

    public function increaseRegisteredCustomersCount(Person $person): void
    {
        $address = Address::with('city')->where('owner_id', '=', $person->id)->first();

        $mini_grid_id = $address->city->mini_grid_id;

        ClusterMetaData::query()->where('mini_grid_id', '=', $mini_grid_id)->first()->increment('registered_customers');
    }

    public function decreaseRegisteredCustomersCount(Person $person): void
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
        $events->listen(
            'cluster_meta.connected_meters.increase',
            '\App\Listeners\ClusterMetaDataListener@increaseConnectedMetersCount'
        );
        $events->listen(
            'cluster_meta.connected_meters.decrease',
            '\App\Listeners\ClusterMetaDataListener@decreaseConnectedMetersCount'
        );
        $events->listen(
            'cluster_meta.registered_customers.increase',
            '\App\Listeners\ClusterMetaDataListener@increaseRegisteredCustomersCount'
        );
        $events->listen(
            'cluster_meta.registered_customers.decrease',
            '\App\Listeners\ClusterMetaDataListener@decreaseRegisteredCustomersCount'
        );
    }
}
