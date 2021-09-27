<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\PersonService;
use App\Models\MaintenanceUsers;
use App\Models\Person\Person;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * @group   Maintenance User
 * Class MaintenanceUserController
 * @package App\Http\Controllers
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
    private $maintenanceUsers;


    public function __construct(MaintenanceUsers $maintenance_users, Person $person)
    {
        $this->person = $person;
        $this->maintenanceUsers = $maintenance_users;
    }

    /**
     * List of all Maintenance Users
     * @responseFile responses/maintenanceUser/maintenance.users.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $maintenance_user_list = $this->maintenanceUsers::with('person')->get();
        return new ApiResource($maintenance_user_list);
    }

    /**
     * Create a new maintenance user.
     *
     * @bodyParam name string required
     * @bodyParam surname string required
     * @bodyParam phone string required
     * @bodyParam person_id int required
     * @bodyParam mini_grid_id int required
     * @param MaintenanceRequest $request
     * @return JsonResponse
     */
    public function store(MaintenanceRequest $request): JsonResponse
    {
        $phone = $request->get('phone');

        try {
            $person = $this->person->whereHas(
                'addresses',
                static function ($q) use ($phone) {
                    $q->where('phone', $phone);
                }
            )->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $personService = App::make(PersonService::class);
            $person = $personService->createFromRequest($request);
        }

        $maintenanceUser = $this->maintenanceUsers::create(
            [
            'person_id' => $person->id,
            'mini_grid_id' => $request->get('mini_grid_id'),
            ]
        );

        return
            (new ApiResource($maintenanceUser))
            ->response()
            ->setStatusCode(201);
    }
}
