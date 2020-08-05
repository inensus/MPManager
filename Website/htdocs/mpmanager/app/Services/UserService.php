<?php


namespace App\Services;

use App\Exceptions\MailNotSentException;
use App\Helpers\PasswordGenerator;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class UserService implements IUserService
{

    public function create(array $userData)
    {
        return User::query()->create([
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
            $user->addresses()->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            return null;
        }

        if ($user->address === null) {
            if ($createOnFailure) {
                $user->address()->create(request()->only('phone', 'email', 'street', 'city_id', 'is_primary'));
                return $user;
            }
            return null;

        }

        $user->address()->update(
            request()->only('email', 'phone', 'street', 'city_id', 'is_primary')
        );
        return $user;
    }


    public function resetPassword(string $email)
    {
        try {
            $newPassword = PasswordGenerator::generatePassword();
        } catch (Exception $exception) {
            $newPassword = time();
        }
        try {
            $user = User::query()->where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $x) {
            return null;
        }

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
                'id' => '4
                78efhd3497gvfdhjkwgsdjkl4ghgdf',
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
