<?php

namespace App\Services;

use App\Entities\UserEntity;
use App\Models\User;

interface IUserService
{
    public function create(array $userData);

    public function update(User $user, $data);

    public function resetPassword(string $email);


    public function list($relations);


}
