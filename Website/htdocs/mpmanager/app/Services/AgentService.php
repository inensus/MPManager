<?php

namespace App\Services;


use App\Helpers\PasswordGenerator;
use App\Http\Services\AddressService;
use App\Http\Services\CountryService;
use App\Models\Address\Address;
use App\Models\Agent;

use App\Models\Country;
use App\Models\Person\Person;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;


class AgentService implements IUserService
{

    private $countryService;
    private $addressService;


    public function __construct(CountryService $countryService, AddressService $addressService)
    {
        $this->countryService = $countryService;
        $this->addressService = $addressService;

    }

    public function createFromRequest(Request $request): Model
    {

        $person = Person::query()->create(request()->only([
            'title',
            'education',
            'name',
            'surname',
            'birth_date',
            'sex',
            'is_customer',
        ]));


        $country = $this->countryService->getByCode(request('nationality') ?? 'TZ');
        if ($country !== null) {
            $person = $this->addCitizenship($person, $country);
        }

        $addressParams = [
            'city_id' => request('city_id') ?? 1,
            'email' => request('email') ?? "",
            'phone' => request('phone') ?? "",
            'street' => request('street') ?? "",
            'is_primary' => 1,
        ];

        $address = $this->addressService->instantiate($addressParams);


        $agent = Agent::query()->create([
            'person_id' => $person->id,
            'name' => $person->name,
            'password' => $request['password'],
            'email' => $request['email'],
            'mini_grid_id' => $request['city_id'],
            'agent_commission_id' => $request['agent_commission_id']
        ]);

        $this->addressService->assignAddressToOwner($agent, $address);


        return $agent;
    }


    public function update($agent, $data)
    {


        $person = Person::find($data['personId']);
        $person->name = $data['name'];
        $person->surname = $data['surname'];
        $person->sex = $data['gender'];
        $person->birth_date = $data['birthday'];
        $person->update();

        $address = Address::query()->where('owner_type', 'agent')->where('owner_id', $data['id'])->firstOrFail();

        $address->phone = $data['phone'];
        $address->update();

        $agent->name = $data['name'];
        $agent->agent_commission_id = $data['commissionTypeId'];

        $agent->update();

        return Agent::with(['person', 'addresses', 'miniGrid', 'commission'])
            ->where('id', $agent->id)->firstOrFail();


    }

    public function updateDevice($agent, $deviceId)
    {
        $agent->device_id = $deviceId;
        $agent->update();
        $agent->fresh();

    }

    public function resetPassword(string $email)
    {
        try {
            $newPassword = PasswordGenerator::generatePassword();
        } catch (Exception $exception) {
            $newPassword = time();
        }
        try {
            $agent = Agent::query()->where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $x) {
            $message = 'Invalid email.';
            return $message;
        }

        $agent->password = $newPassword;
        $agent->update();
        $agent->fresh();
        return $newPassword;
    }

    public function list($relations): LengthAwarePaginator
    {
        return Agent::with(['addresses', 'miniGrid'])->paginate(config('settings.paginate'));
    }

    public function setFirebaseToken($agent, $firebaseToken)
    {
        $agent->fire_base_token = $firebaseToken;
        $agent->update();
        $agent->fresh();
    }

    public function getAgentBalance($agent)
    {
        return $agent->balance;
    }

    public function searchAgent($searchTerm, $paginate)
    {
        if ($paginate === 1) {
            return Agent::with('miniGrid')->WhereHas('miniGrid', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%');
            })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')->paginate(15);
        }

        return Agent::with('miniGrid')->WhereHas('miniGrid', function ($q) use ($searchTerm) {
            $q->where('name', 'LIKE', '%' . $searchTerm . '%');
        })->orWhere('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')->get();

    }

    // associates the person with a country
    public function addCitizenship(Person $person, Country $country): Model
    {
        return $person->citizenship()->associate($country);
    }

    public function create(array $userData)
    {
        // TODO: Implement create() method.
    }

    public function getAgentDetail($agent)
    {
        return Agent::with(['person', 'addresses', 'miniGrid', 'commission'])
            ->where('id', $agent->id)->firstOrFail();
    }

    public function deleteAgent($agent)
    {
        $agent->delete();

    }
}
