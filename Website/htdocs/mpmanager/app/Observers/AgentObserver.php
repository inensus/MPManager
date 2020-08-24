<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 31.07.18
 * Time: 19:53
 */

namespace App\Observers;

use App\Models\Agent;
use App\Models\Person\Person;
use Illuminate\Support\Facades\Log;

class AgentObserver
{
    public function created(Agent $agent)
    {

    }

    public function updated(Agent $agent): void
    {
        Log::debug($agent->id . 'updated');
    }


    public function deleted(Agent $agent): void
    {

        $person = Person::find($agent->person_id);
        $person->delete();
        foreach ($agent->addresses()->get() as $address) {
            $address->delete();
        }

    }
}
