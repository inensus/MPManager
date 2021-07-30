<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 31.07.18
 * Time: 19:53
 */

namespace App\Observers;

use App\Models\Person\Person;
use Illuminate\Support\Facades\Log;

class PersonObserver
{

    /**
     * Handle the Person "updated" event
     *
     * @param  Person $person
     * @return void
     */
    public function updated(Person $person): void
    {
        Log::debug($person->id . 'updated');
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  Person $person
     * @return void
     */
    public function deleted(Person $person): void
    {
        //delete all associated roles
        $person->roleOwner()->get();

        /*
        in order to fire the deleted event on relation models,
        the model should be pulled first and deleted afterwards.
        */
        if($person->is_customer == 1){
            event('cluster_meta.registered_customers.decrement', $person);
        }
        // delete all addresses
        foreach ($person->addresses()->get() as $address) {
            $address->delete();
        }
        //delete all meter-parameters
        foreach ($person->meters()->get() as $meter) {
            $meter->delete();
        }
        // delete all transactions which are belong to that person
        foreach ($person->transactions()->get() as $transaction) {
            $transaction->delete();
        }
    }

    /**
     * Handle the User "created" event.
     *
     * @param  Person $person
     * @return void
     */
    public function created(Person $person)
    {
        if($person->is_customer === 1){
            event('cluster_meta.registered_customers.increment', $person);
        }

    }
}
