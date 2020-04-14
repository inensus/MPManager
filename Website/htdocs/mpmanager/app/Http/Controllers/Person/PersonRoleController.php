<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 12.07.18
 * Time: 11:12
 */

namespace App\Http\Controllers\Person;

use App\Repositories\Roles;

/**
 * Class PersonRoleController
 * @package App\Http\Controllers\Person
 *
 * @group Customer Management
 */
class PersonRoleController
{
    public function index(String $roleType = null, Roles $role)
    {

        $list = $role->all($roleType);
        $title = $roleType === null ? 'With no assigned Role' : ucfirst($roleType);
        return view('layouts.people.role-list', compact('list', 'title'));
    }

}
