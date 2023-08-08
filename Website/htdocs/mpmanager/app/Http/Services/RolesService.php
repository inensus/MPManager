<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.07.18
 * Time: 18:59
 */

namespace App\Http\Services;

use App\Models\Role\RoleDefinition;
use App\Models\Role\RoleInterface;
use App\Models\Role\Roles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RolesService
{
    public function __construct(private Roles $role)
    {
    }

    public function create(RoleDefinition $definition): Roles
    {
        $this->role->definitions()->associate($definition);
        return $this->role;
    }
}
