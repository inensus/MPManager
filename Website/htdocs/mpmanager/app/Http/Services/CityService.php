<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 2019-03-13
 * Time: 19:24
 */

namespace App\Http\Services;

use App\Models\City;
use App\Models\Person\Person;

class CityService
{
    public function __construct(private Person $person)
    {
    }

    public function getClusterPopulation(int $clusterId, bool $onlyCustomers = true): int
    {
        if ($onlyCustomers) {
            $population = $this->person->newQuery()
                ->where('is_customer', 1)
                ->whereHas(
                    'addresses',
                    function ($q) use ($clusterId) {
                        $q->where('is_primary', 1)->whereHas(
                            'city',
                            function ($q) use ($clusterId) {
                                $q->where('cluster_id', $clusterId);
                            }
                        );
                    }
                )->count();
        } else {
            $population = $this->person->newQuery()
                ->whereHas(
                    'addresses',
                    function ($q) use ($clusterId) {
                        $q->where('is_primary', 1)->whereHas(
                            'city',
                            function ($q) use ($clusterId) {
                                $q->where('cluster_id', $clusterId);
                            }
                        );
                    }
                )->count();
        }

        return $population;
    }
}
