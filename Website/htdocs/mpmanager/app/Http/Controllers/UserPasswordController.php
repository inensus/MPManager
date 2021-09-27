<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;
use App\Services\IUserService;
use Illuminate\Http\Response;

/**
 * @group   User
 * Class UserPasswordController
 * @package App\Http\Controllers
 */
class UserPasswordController extends Controller
{
    private $userService;
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Forgot Password
     * Admin reset password request.
     * @urlParam email password
     * @param AdminResetPasswordRequest $request
     * @param Response $response
     * @return Response|object
     */
    public function forgotPassword(AdminResetPasswordRequest $request, Response $response)
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

    /**
     * Change Password
     * Admin change password request.
     * @urlParam userId required.
     * @bodyParam password string required
     * @param User $user
     * @param UserChangePasswordRequest $changePasswordRequest
     * @return ApiResource
     */
    public function update(User $user, UserChangePasswordRequest $changePasswordRequest)
    {
        return  new ApiResource($this->userService->update($user, $changePasswordRequest->all()));
    }
}
