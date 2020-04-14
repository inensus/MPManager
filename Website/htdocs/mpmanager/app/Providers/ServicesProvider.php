<?php

namespace App\Providers;

use App\Http\Services\AddressService;
use App\Http\Services\CountryService;
use App\Http\Services\RolesService;
use App\Http\Services\PersonService;
use App\Models\Address\Address;
use App\Models\AssetPerson;
use App\Models\Battery;
use App\Models\Country;

use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;
use App\Models\PV;
use App\Models\Role\RoleDefinition;
use App\Models\Role\Roles;
use App\Observers\AddressesObserver;
use App\Observers\AssetPersonObserver;
use App\Observers\BatteryObserver;
use App\Observers\MeterParameterObserver;
use App\Observers\PersonObserver;
use App\Observers\PVObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;


class ServicesProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Person::observe(PersonObserver::class);
        Address::observe(AddressesObserver::class);
        MeterParameter::observe(MeterParameterObserver::class);
        AssetPerson::observe(AssetPersonObserver::class);
        PV::observe(PVObserver::class);
        Battery::observe(BatteryObserver::class);
        Horizon::auth(function ($request) {
            return true;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(AddressService::class, function ($app) {
            return new AddressService($this->app->make(Address::class));
        });

        $this->app->bind(RolesService::class, function ($app) {
            return new RolesService($this->app->make(Roles::class), $this->app->make(RoleDefinition::class));
        });

        $this->app->bind(PersonService::class, function ($app) {
            return new PersonService($this->app->make(Person::class));
        });
        $this->app->bind(CountryService::class, function ($app) {
            return new CountryService($this->app->make(Country::class));
        });

    }
}
