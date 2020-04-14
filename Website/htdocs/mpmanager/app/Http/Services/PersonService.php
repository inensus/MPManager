<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.07.18
 * Time: 18:59
 */

namespace App\Http\Services;


use App;
use App\Models\Address\Address;
use App\Models\MaintenanceUsers;
use App\Models\Person\Person;
use App\Models\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PersonService
{
    /**
     * @var Person
     */
    private $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function createFromRequest(Request $request): Model
    {

        $person = $this->person::create(request()->only([
            'title',
            'education',
            'name',
            'surname',
            'birth_date',
            'sex',
            'is_customer',
        ]));


        $countryService = App::make(CountryService::class);
        $country = $countryService->getByCode(request('nationality') ?? 'TZ');
        $person = $this->addCitizenship($person, $country);

        $addressService = App::make(AddressService::class);

        $addressParams = [
            'city_id' => request('city_id') ?? 1,
            'email' => request('email') ?? "",
            'phone' => request('phone') ?? "",
            'street' => request('street') ?? "",
            'is_primary' => 1,
        ];

        $address = $addressService->instantiate($addressParams);

        $addressService->assignAddressToOwner($person, $address);
        return $person;
    }


    // associates the person with a country
    public function addCitizenship(Person $person, Country $country): Model
    {
        return $person->citizenship()->associate($country);
    }

    //assign an address to the person
    public function addAddress(Person $person, Address $address): Model
    {
        return $person->addresses()->save($address);
    }

    public function getDetails(int $personID, bool $allRelations = false)
    {
        if (!$allRelations) {
            return $this->person->find($personID);
        }
        return $this->person::with([
            'addresses' =>
                function ($q) {
                    return $q->orderBy('is_primary')->get();
                },
            'citizenship',
            'roleOwner.definitions',
            'meters.meter',
        ])->find($personID);
    }

    /**
     * @param string $searchTerm could either phone, name or surname
     *
     * @return Person[]|LengthAwarePaginator|Builder[]|Collection
     */
    public function searchPerson($searchTerm, $paginate)
    {
        if ($paginate === 1) {
            return $this->person::with('addresses.city', 'meters.meter')->whereHas('addresses',
                function ($q) use ($searchTerm) {
                    $q->where('phone', 'LIKE', '%' . $searchTerm . '%');
                })->orWhereHas('meters.meter', function ($q) use ($searchTerm) {
                $q->where('serial_number', 'LIKE', '%' . $searchTerm . '%');
            })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('surname', 'LIKE', '%' . $searchTerm . '%')->paginate(15);
        }

        return $this->person::with('addresses.city', 'meters.meter')->whereHas('addresses',
            function ($q) use ($searchTerm) {
                $q->where('phone', 'LIKE', '%' . $searchTerm . '%');
            })->orWhereHas('meters.meter', function ($q) use ($searchTerm) {
            $q->where('serial_number', 'LIKE', '%' . $searchTerm . '%');
        })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('surname', 'LIKE', '%' . $searchTerm . '%')->get();

    }


    public function createMaintenancePerson(Request $request)
    {
        $this->person->is_customer = 0;
        $this->person->name = $request->get('name');
        $this->person->surname = $request->get('surname');
        $this->person->sex = $request->get('sex');

        $this->person->save();


        $addressService = App::make(AddressService::class);

        $addressParams = [
            'city_id' => $request->get('city_id') ?? 1,
            'email' => $request->get('email') ?? '',
            'phone' => $request->get('phone') ?? '',
            'street' => $request->get('street') ?? '',
            'is_primary' => 1,
        ];

        $address = $addressService->instantiate($addressParams);

        $addressService->assignAddressToOwner($this->person, $address);


        $maintenance = App::make(MaintenanceUsers::class);

        $maintenance->person_id = $this->person->id;
        $maintenance->mini_grid_id = $request->get('mini_grid_id');
        $maintenance->save();

        return $this->person;
    }
}
