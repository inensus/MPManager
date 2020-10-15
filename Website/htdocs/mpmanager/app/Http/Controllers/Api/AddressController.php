<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Http\Requests\CreateAddressRequest;
use App\Models\Address\Address;
use App\Exceptions\ValidationException;
use App\Http\Resources\ApiResource;
use App\Models\Person\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Addresses
 * Class AddressController
 * @package App\Http\Controllers
 */
class AddressController extends Controller
{
    /**
     * The address model.
     * @var Address
     */
    private $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * List
     * A list of all registered addresses
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(
            $this->address::all()
        );
    }

    /**
     * Detail
     * The detail of a given address id
     * @urlParam id int required
     * @param integer $id
     * @return ApiResource
     */
    public function show($id): ApiResource
    {
        return new ApiResource(
            $this->address->findOrFail($id)
        );
    }


    /**
     * @group People
     * Create a new address
     * It adds a new address to the given person.
     * @urlParam person required The ID of person
     *
     * @bodyParam email string. Example: johndoe@mail.com
     * @bodyParam phone string.
     * @bodyParam street string.
     * @bodyParam city_id string required.
     * @bodyParam primary string required. The flag if the new address is the primary address of that person
     * @param Person $person
     * @param CreateAddressRequest $request
     * @return ApiResource
     */
    public function store(Person $person, CreateAddressRequest $request): ApiResource
    {
        $this->address->email = $request->get('email') ?? '';
        $this->address->phone = $request->get('phone') ?? '';
        $this->address->street = $request->get('street') ?? '';
        $this->address->city_id = $request->get('city_id');
        $this->address->is_primary = $request->get('primary') ?? 0;
        if ($this->address->is_primary === 1) { // set old primary address to not primary
            $person->addresses()->where('is_primary', 1)->update(['is_primary' => 0]);
        }
        $this->address->owner()->associate($person);

        $this->address->save();
        return new ApiResource($this->address->with('city')->where('id', $this->address->id)->first());
    }

    /**
     * @group UserManagement
     * Create a new address
     * @bodyParam email string. Example: johndoe@mail.com
     * @bodyParam phone string.
     * @bodyParam street string.
     * @bodyParam city_id string required
     * @param User $user
     * @return ApiResource
     *
     * @urlParam user required The ID of Admin
     */
    public function storeAdmin(User $user, CreateAddressRequest $request): ApiResource
    {
        $this->address->email = $request->get('email') ?? '';
        $this->address->phone = $request->get('phone') ?? '';
        $this->address->street = $request->get('street') ?? '';
        $this->address->city_id = $request->get('city_id');
        $this->address->is_primary = 1;
        //delete address if exists
        $user->address()->delete();

        $this->address->owner()->associate($user);

        $this->address->save();
        return new ApiResource($this->address->with('city')->where('id', $this->address->id)->first());
    }

    /**
     * @group UserManagement
     * Get Address
     * @urlParam user required The ID of Admin
     * @param User $user
     * @param Request $request
     * @return ApiResource
     */
    public function adminAddress(User $user, Request $request)
    {
        $address = $user->addressDetails()->first();
        return new ApiResource($address);
    }

    /**
     * Update Address
     * @group UserManagement
     * @bodyParam email string. Example: johndoe@mail.com
     * @bodyParam phone string.
     * @bodyParam street string.
     * @bodyParam city_id string required
     * @param User $user
     * @param CreateAddressRequest $request
     * @return ApiResource
     */
    public function updateAdmin(User $user, CreateAddressRequest $request)
    {
        try {
            $address = $user->address()->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            return new ApiResource(['no adress found  ']);
        }
        $address->email = $request->get('email') ?? $address->email;
        $address->phone = $request->get('phone') ?? $address->phone;
        $address->street = $request->get('street') ?? $address->street;
        $address->city_id = $request->get('city_id');


        return new ApiResource($address);
    }

    /**
     * @group People
     * Update Address
     * @urlParam person required The ID of person
     *
     * @bodyParam id int required. The ID of to be updated address
     * @bodyParam email string. Example: johndoe@mail.com
     * @bodyParam phone string.
     * @bodyParam street string.
     * @bodyParam city_id string required.
     * @bodyParam primary string required. The flag if the new address is the primary address of that person
     * @param Person $person
     * @return ApiResource
     * @throws ValidationException
     */
    public function update(Person $person): ApiResource
    {
        $validation = Validator::make(request()->all(), Address::$rules);
        if ($validation->fails()) {
            throw new ValidationException($validation->errors());
        }
        $address = $this->address->find(request('id'));
        $address->email = request('email') ?? '';
        $address->phone = request('phone') ?? '';
        $address->street = request('street') ?? '';
        $address->city_id = request('city_id');
        $address->is_primary = request('primary') ?? 0;
        if ($address->is_primary) { // set old primary address to not primary
            $person->addresses()->where('is_primary', 1)->update(['is_primary' => 0]);
        }
        $address->save();
        return new ApiResource($address->with('city')->where('id', $address->id)->first());
    }
}
