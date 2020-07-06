<?php

namespace App\Observers;

use App\Models\AppliancePerson;
use App\Models\ApplianceRate;

class AppliancePersonObserver
{
    /**
     * @var ApplianceRate
     */
    private $applianceRate;

    public function __construct(ApplianceRate $applianceRate)
    {
        $this->applianceRate = $applianceRate;
    }

    /**
     * Handle the appliance person "created" event.
     *
     * @param AppliancePerson $appliancePerson
     * @return void
     */
    public function created(AppliancePerson $appliancePerson):void
    {
        $base_time = time();
        foreach (range(1, $appliancePerson->rate_count) as $rate) {
            if ((int)$rate === (int)$appliancePerson->rate_count) {
                //last rate
                $rate_cost = $appliancePerson->total_cost - (($rate - 1) * floor($appliancePerson->total_cost / $appliancePerson->rate_count));

            } else {
                $rate_cost = floor($appliancePerson->total_cost / $appliancePerson->rate_count);
            }
            $rate_date = date('Y-m-01', strtotime('+' . $rate . ' month', $base_time));
            $this->applianceRate::create([
                'appliance_person_id' => $appliancePerson->id,
                'rate_cost' => $rate_cost,
                'remaining' => $rate_cost,
                'due_date' => $rate_date
            ]);
        }
    }

    /**
     * Handle the appliance person "updated" event.
     *
     * @param AppliancePerson $appliancePerson
     * @return void
     */
    public function updated(AppliancePerson $appliancePerson)
    {
        //
    }

    /**
     * Handle the appliance person "deleted" event.
     *
     * @param AppliancePerson $appliancePerson
     * @return void
     */
    public function deleted(AppliancePerson $appliancePerson)
    {
        //
    }

    /**
     * Handle the asapplianceset person "restored" event.
     * @param AppliancePerson $appliancePerson
     * @return void
     */
    public function restored(AppliancePerson $appliancePerson)
    {
        //
    }

    /**
     * Handle the appliance person "force deleted" event.
     *
     * @param AppliancePerson $appliancePerson
     * @return void
     */
    public function forceDeleted(AppliancePerson $appliancePerson)
    {
        //
    }
}
