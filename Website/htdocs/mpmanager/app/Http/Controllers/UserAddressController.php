<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAddressRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Services\UserAddressService;
use Illuminate\Http\Request;

/**
 * @group   User Address
 * Class UserAddressController
 * @package App\Http\Controllers
 */
class UserAddressController extends Controller
{

    private $userAddressService;
    public function __construct(UserAddressService $userAddressService)
    {
        $this->userAddressService = $userAddressService;
    }

    /**
     * Create
     * Create a new address and associate with person.
     * @urlParam personId required
     * @bodyParam email string
     * @bodyParam phone string
     * @bodyParam street string
     * @bodyParam city_id int
     * @bodyParam is_primary bool
     * @param User $user
     * @param CreateAddressRequest $request
     * @return ApiResource
     */
    public function store(User $user, CreateAddressRequest $request): ApiResource
    {
        return new ApiResource($this->userAddressService->update($user, $request->all()));
    }

    /**
     * Detail
     * Details of the address for specified user.
     * @urlParam userId required
     * @param User $user
     * @param Request $request
     * @return ApiResource
     */
    public function admin(User $user, Request $request)
    {
        $address = $user->addressDetails()->first();
        return new ApiResource($address);
    }

    /**
     * Update
     * Update of the address for specified user.
     * @urlParam userId required
     * @bodyParam email string
     * @bodyParam phone string
     * @bodyParam street string
     * @bodyParam city_id int
     * @bodyParam is_primary bool
     * @param User $user
     * @param CreateAddressRequest $request
     * @return ApiResource
     */
    public function update(User $user, CreateAddressRequest $request)
    {
        return new ApiResource($this->userAddressService->update($user, $request->all()));
    }
}
