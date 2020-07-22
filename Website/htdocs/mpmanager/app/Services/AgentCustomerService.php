<?php

namespace App\Services;


use App\Models\Agent;
use App\Models\Person\Person;

class AgentCustomerService
{


    private $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function list(Agent $agent)
    {

     $miniGridId = $agent->mini_grid_id;
      return Person::with([
            'addresses' => function ($q) {
                return $q->where('is_primary', 1);
            },
            'addresses.city',
            'meters.meter',
        ])
            ->where('is_customer', 1)
            ->whereHas('addresses',function ($q) use ($miniGridId){
                $q->whereHas('city', function ($q) use ($miniGridId) {
                    $q->where('mini_grid_id', $miniGridId);
                });
            })
            ->paginate(config('settings.paginate'));




    }


}
