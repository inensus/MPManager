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
    /**
     * @var Roles
     */
    private $role;
    /**
     * @var RoleDefinition
     */
    private $definiton;

    public function __construct(Roles $role, RoleDefinition $definition)
    {
        $this->role = $role;
        $this->definiton = $definition;
    }

    public function findOrCreateRoleDefinition(string $roleName)
    {
        return $this->definiton->firstOrCreate(['role_name' => $roleName]);
    }

    /**
     * @return Builder[]|Collection
     *
     * @psalm-return \Illuminate\Database\Eloquent\Collection|array<array-key, \Illuminate\Database\Eloquent\Builder>
     */
    public function findRoleByDefinition(RoleDefinition $definition)
    {
        return $this->role->with()->get();
    }

    /**
     * @return Model|false
     */
    public function attachToOwner(RoleInterface $roleOwner, Roles $role) //person or a company
    {
        return $roleOwner->roleowner()->save($role);
    }

    public function create(RoleDefinition $definition): Roles
    {
        $this->role->definitions()->associate($definition);
        return $this->role;
    }
}
