<?php

namespace App\Http\Controllers;

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
 * @group   Address
 * Class AddressController
 * @package App\Http\Controllers
 */

class AddressController extends Controller
{
    private $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * List Addresses
     * A list of all registered addresses
     * @responseFile responses/people/person.addresses.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(
            $this->address::all()
        );
    }

    /**
     * Detail Addresses
     * The detail of a given address id
     * @urlParam userId required
     * @responseFile responses/people/person.addresses.list.json
     * @param $id
     * @return ApiResource
     */
    public function show($id): ApiResource
    {
        return new ApiResource(
            $this->address->findOrFail($id)
        );
    }


    /**
     * Create Address
     * It adds a new address to the given person.
     * @urlParam personId required The ID of person
     *
     * @bodyParam email string. Example: johndoe@mail.com
     * @bodyParam phone string.
     * @bodyParam street string.
     * @bodyParam city_id string required.
     * @bodyParam primary string required. The flag if the new address is the primary address of that person
     * @param     Person               $person
     * @param     CreateAddressRequest $request
     * @return    ApiResource
     */
    public function store(Person $person, CreateAddressRequest $request): ApiResource
    {
        $this->address->email = $request->get('email') ?? '';
        $this->address->phone = $request->get('phone') ?? '';
        $this->address->street = $request->get('street') ?? '';
        $this->address->city_id = $request->get('city_id');
        $this->address->is_primary = $request->get('primary') ?? 0;
        if ($this->address->is_primary) { // set old primary address to not primary
            $person->addresses()->where('is_primary', 1)->update(['is_primary' => 0]);
        }
        $this->address->owner()->associate($person);

        $this->address->save();
        $person->update(
            [
                'updated_at' => date('Y-m-d h:i:s')]
        );
        return new ApiResource($this->address->with('city')->where('id', $this->address->id)->first());
    }


    /**
     * Update Address
     *  Updates address for the given person
     * @urlParam personId required int
     *
     * @bodyParam email string
     * @bodyParam phone string
     * @bodyParam street string
     * @bodyParam city_id int
     * @bodyParam primary bool
     *
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
        $person->update(
            [
                'updated_at' => date('Y-m-d h:i:s')]
        );
        return new ApiResource($address->with('city')->where('id', $address->id)->first());
    }
}
