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

    /**
     * @var City
     */
    private $city;
    /**
     * @var Person
     */
    private $person;

    public function __construct(City $city, Person $person)
    {
        $this->city = $city;
        $this->person = $person;
    }

    public function getCityPopulation($cityId, $onlyCustomers = true)
    {
        if ($onlyCustomers) {

            $population = $this->person
                ->where('is_customer', 1)
                ->whereHas('addresses', function ($q) use ($cityId) {
                    $q->where('city_id', $cityId)->where('is_primary', 1);
                })->count();
        } else {
            $population = $this->person->whereHas('addresses', function ($q) use ($cityId) {
                $q->where('city_id', $cityId)->where('is_primary', 1);
            })->count();
        }

        return $population;
    }

    public function getClusteropulation($clusterId, $onlyCustomers = true)
    {
        if ($onlyCustomers) {
            $population = $this->person
                ->where('is_customer', 1)
                ->whereHas('addresses', function ($q) use ($clusterId) {
                    $q->where('is_primary', 1)->whereHas('city', function ($q) use ($clusterId) {
                        $q->where('cluster_id', $clusterId);
                    });
                })->count();
        } else {
            $population = $this->person->whereHas('addresses', function ($q) use ($clusterId) {
                $q->where('is_primary', 1)->whereHas('city', function ($q) use ($clusterId) {
                    $q->where('cluster_id', $clusterId);
                });
            })->count();
        }


        return $population;
    }
}
