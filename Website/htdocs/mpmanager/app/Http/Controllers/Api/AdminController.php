<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 07.09.18
 * Time: 11:07
 */

namespace App\Http\Controllers;


use App\Exceptions\MailNotSentException;
use App\Http\Requests\AdminResetPasswordRequest;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group UserManagement
 * Class AdminController
 * @package App\Http\Controllers
 *
 *
 */
class AdminController extends Controller
{
    /**
     * @var User
     */
    private $user;

    /**
     * AdminController constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function auth()
    {

        return ['id' => Auth::id(), 'name' => Auth::user()->name, 'email' => Auth::user()->email];
    }

    /**
     * List
     * lists all registered users
     * @return ApiResource
     */
    public function index(Request $request)
    {
        $addressRelation = $request->get('address') ?? 0;
        if ($addressRelation) {
            $users = $this->user->select('id', 'name', 'email')->with('address')->get();
        } else {
            $users = $this->user->select('id', 'name', 'email')->get();

        }
        return new ApiResource($users);
    }

    /**
     * Create new Admin
     *
     * @bodyParam email string required
     * @bodyParam name string required
     * @bodyParam password string required
     *
     * @param CreateAdminRequest $request
     * @return ApiResource
     *
     */
    public function store(CreateAdminRequest $request)
    {
        $password = Hash::make($request->get('password'));
        $admin = $this->user->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $password,
        ]);
        return new ApiResource($admin);
    }

    /**
     * Update
     * @param Request $request
     * @urlParam user required The ID of the User to be updated
     * @bodyParam email string
     * @bodyParam name string
     * @bodyParam password string
     * @return ApiResource
     */
    public function update(User $user, Request $request): ApiResource
    {
        $user->email = request()->get('email') ?? $user->email;
        $user->name = request()->get('name') ?? $user->name;
        if ($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();
        return new ApiResource($user);
    }

    public function forgotPassword(AdminResetPasswordRequest $request, Response $response): Response
    {

        $email = $request->get('email');

        $user = $this->user->where('email', $email)->first();


        //generate a new random password
        $passwordLength = 6;
        try {
            $password = random_int(10 ** ($passwordLength - 1), (10 ** $passwordLength) - 1);
            $encryptedPassword = Hash::make($password);
        } catch (Exception $e) {
            return $response->setStatusCode(422)->setContent([
                'data' => [
                    'message' => 'Failed to generate a new password. Please try it again later.',
                    'status_code' => 409
                ]
            ]);
        }

        $user->password = $encryptedPassword;
        $mailer = resolve('MailProvider');

        //send email to user
        try {
            $mailer->sendPlain(
                $user->email,
                'Your new Password | Micro Power Manager',
                'You can use ' . $password . ' to Login. <br> Please don\'t forget to change your password.'
            );
        } catch (MailNotSentException $x) {
            return $response->setStatusCode(422)->setContent([
                'data' => [
                    'message' => 'Failed to send password email. Please try it again later.',
                    'status_code' => 409
                ]
            ]);
        } catch (Exception $x) {
            return $response->setStatusCode(422)->setContent([
                'data' => [
                    'message' => 'System failed. Please try it again later.',
                    'status_code' => 409
                ]
            ]);
        }
        $user->save();


        return $response->setStatusCode(200)->setContent([
            'data' => [
                'message' => 'New password sent to your email address',
                'status_code' => 200
            ]
        ]);

    }
}
