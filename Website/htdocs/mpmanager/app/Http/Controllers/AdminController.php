<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 07.09.18
 * Time: 11:07
 */

namespace App\Http\Controllers;

use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Services\IUserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   UserManagement
 * Class AdminController
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{
    /**
     * @var IUserService
     */
    private $userService;

    /**
     * AdminController constructor.
     *
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * List
     * lists all registered users
     *
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {
        $users = $this->userService->list($request->all());
        return new ApiResource($users);
    }

    /**
     * Create new Admin
     *
     * @bodyParam email string required
     * @bodyParam name string required
     * @bodyParam password string required
     *
     * @param  CreateAdminRequest $request
     * @return ApiResource
     */
    public function store(CreateAdminRequest $request): ApiResource
    {
        $admin = $this->userService->create($request->only(['name', 'password', 'email']));

        return new ApiResource($admin);
    }

    /**
     * Update
     *
     * @param     User    $user
     * @param     Request $request
     * @return    ApiResource
     * @urlParam  user required The ID of the User to be updated
     * @bodyParam email string
     * @bodyParam name string
     * @bodyParam password string
     */
    public function update(User $user, Request $request): ApiResource
    {
        $this->userService->update($user, $request->all());

        return new ApiResource($user->fresh());
    }

    /**
     * Forgot password
     *
     * @bodyParam email string
     * @param     AdminResetPasswordRequest $request
     * @param     Response                  $response
     * @return    Response
     */
    public function forgotPassword(AdminResetPasswordRequest $request, Response $response): self
    {
        if (!$this->userService->resetPassword($request->input('email'))) {
            return $response->setStatusCode(422)->setContent(
                [
                'data' => [
                    'message' => 'Failed to send password email. Please try it again later.',
                    'status_code' => 409
                ]
                ]
            );
        }

        return $response->setStatusCode(200)->setContent(
            [
            'data' => [
                'message' => 'New password sent to your email address',
                'status_code' => 200
            ]
            ]
        );
    }
}
