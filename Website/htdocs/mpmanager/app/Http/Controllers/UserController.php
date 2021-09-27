<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdminRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Services\IUserService;
use Illuminate\Http\Request;

/**
 * @group   User
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    private $userService;
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * List of all users.
     * A list of the all users.
     * @responseFile responses/user/users.list.json
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {
        $users = $this->userService->list();
        return new ApiResource($users);
    }

    /**
     * Create a new user.
     * @bodyParam name string required
     * @bodyParam password string  required
     * @bodyParam email string required
     * @param CreateAdminRequest $request
     * @return ApiResource
     */
    public function store(CreateAdminRequest $request): ApiResource
    {
        return new ApiResource($this->userService->create($request->only(['name', 'password', 'email'])));
    }

    /**
     * Details of the specified user.
     * @urlParam userId required.
     * @responseFile responses/user/user.detail.json
     * @param User $user
     * @return ApiResource
     */
    public function show(User $user)
    {
        return new ApiResource($this->userService->get($user->id));
    }

    /**
     * Update of the specified user.
     * @urlParam userId required.
     * @bodyParam name string
     * @bodyParam password string
     * @bodyParam email string
     * @param User $user
     * @param Request $request
     * @return ApiResource
     */
    public function update(User $user, Request $request): ApiResource
    {
        $this->userService->update($user, $request->all());
        return new ApiResource($user->fresh());
    }
}
