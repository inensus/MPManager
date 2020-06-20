<?php


namespace App\Services;

use App\Exceptions\MailNotSentException;
use App\Helpers\PasswordGenerator;
use App\Http\Resources\ApiResource;
use App\Models\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class UserService implements IUserService
{


    public function create(array $userData)
    {
        return User::create([
            'name' => $userData['name'],
            'password' => $userData['password'],
            'email' => $userData['email'],
        ]);
    }

    public function update(User $user, $data): bool
    {
        return $user->update($data);
    }

    public function updateAddress($user, $createOnFailure = true): ?User
    {
        try {
            $user->addresses()->first();
            $user = $this->userRepository->userAddress($userId);
        } catch (ModelNotFoundException $exception) {
            return null;
        }

        if ($user->address === null) {
            if ($createOnFailure) {
                $address = $this->addressRepository->create(
                    request()->only('phone', 'email', 'street', 'city_id', 'is_primary'));

                $user->address = $this->userRepository->bindAddress($user, $address);
                return $user;
            }
            return null;

        }

        $user->address = $this->addressRepository->update(
            request()->only('email', 'phone', 'street', 'city_id', 'is_primary'),
            $user->address->id,
        );
        return $user;
    }


    public function resetPassword(string $email): ?User
    {
        try {
            $newPassword = PasswordGenerator::generatePassword();
        } catch (\Exception $exception) {
            $newPassword = time();
        }
        $user = User::where('email', $email)->first();

        $user->password = $newPassword;
        $user->update();


        //send the new password
        //this part can not extracted as a job, jobs are working async and in case of any issues the system wont be
        // able to send bad http status
        $mailer = resolve('MailProvider');
        try {
            $mailer->sendPlain(
                $user->email,
                'Your new Password | Micro Power Manager',
                'You can use ' . $newPassword . ' to Login. <br> Please don\'t forget to change your password.'
            );
        } catch (MailNotSentException $exception) {
            Log::debug('Failed to reset password', [
                'id' => '478efhd3497gvfdhjkwgsdjkl4ghgdf',
                'message' => 'Password reset email for ' . $user->email . ' failed',
                'reason' => $exception->getMessage(),
            ]);
            return null;
        }

        return $user->fresh();
    }

    /**
     * User list with optional address relation
     * @param $relations
     * @return LengthAwarePaginator
     */
    public function list($relations): LengthAwarePaginator
    {
        $user = User::query()->select('id', 'name', 'email');
        // the only supported filter is currently address
        if (array_key_exists('address', $relations)) {
            $user->with('address');
        }

        return $user->paginate();
    }
}
