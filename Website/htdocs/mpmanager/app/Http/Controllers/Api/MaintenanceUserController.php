<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\RolesService;
use App\Http\Services\PersonService;
use App\Models\MaintenanceUsers;
use App\Models\Person\Person;
use App\Models\Role\Roles;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * @group Maintenance User
 * Class MaintenanceUserController
 * @package App\Http\Controllers\Api
 */

class MaintenanceUserController extends Controller
{
    private $roles;
    /**
     * @var Person
     */
    private $person;
    /**
     * @var MaintenanceUsers
     */
    private $maintenance_users;


    public function __construct(MaintenanceUsers $maintenance_users, Person $person)
    {
        $this->person = $person;
        $this->maintenance_users = $maintenance_users;
    }

    /**
     * List
     * List of all Maintenance Users
     * @return ApiResource
     */

    public function index(): ApiResource
    {
        $maintenance_user_list = $this->maintenance_users::with('person')->get();
        return new ApiResource($maintenance_user_list);
    }

    /**
     * Create
     * Create a new Maintenance User
     * @bodyParam title string
     * @bodyParam name string required
     * @bodyParam surname string required
     * @bodyParam birth_date date
     * @bodyParam sex string
     * @bodyParam education string
     * @bodyParam city_id int
     * @bodyParam street string
     * @bodyParam email string
     * @bodyParam phone string required
     * @bodyParam nationality int
     * @param MaintenanceRequest $request
     * @return \Illuminate\Http\JsonResponse|object
     */
    public function store(MaintenanceRequest $request)
    {
        $phone = $request->get('phone');

        //try to identify the person via phone number
        try {
            $person = $this->person->whereHas('addresses', static function ($q) use ($phone) {
                $q->where('phone', $phone);
            })->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $personService = App::make(PersonService::class);
            $person = $personService->createFromRequest($request);
        }

        $maintenance_user = $this->maintenance_users::create([
            'person_id' => $person->id,
            'mini_grid_id' => $request->get('mini_grid_id'),
        ]);


        return
            (new ApiResource($maintenance_user))
                ->response()
                ->setStatusCode(201);

    }

    /**
     *
     *
     * Identifies the user with the "key" parameter which is been send with the request
     * TODO: Create an UpdateCustomer request which requires a key parameter with following values (id,phone,email)
     *
     *
     * @param Request $request
     */
    public function update(Request $request)
    {
        $key = $request->get('key');
        $customer = null;
        switch ($key) {
            case 'id':
                //find customer by id
                break;
            case 'phone':
                //find customer by phone
                break;
            case 'email':
                // find customer by email
                break;
        }
    }

    /**
     * Remove
     * Remove of the specified Maintenance User
     * @bodyParam id int required
     * @param $id
     */

    public function destroy($id)
    {
        $roleOwner = $this->roles->findOrFail($id);

        dd($roleOwner);
    }
}
