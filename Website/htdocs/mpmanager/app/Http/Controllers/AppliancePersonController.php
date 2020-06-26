<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AppliancePerson;
use App\Models\ApplianceType;
use App\Models\Person\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppliancePersonController extends Controller
{

    /**
     * @var AppliancePerson
     */
    private $appliancePerson;

    public function __construct(AppliancePerson $appliancePerson)
    {
        $this->appliancePerson = $appliancePerson;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ApplianceType $applianceType
     * @param Person $person
     * @param Request $request
     * @return ApiResource
     */
    public function store(ApplianceType $applianceType, Person $person, Request $request): ApiResource
    {
        $appliancePerson = $this->appliancePerson::create([
            'person_id' => $person->id,
            'appliance_type_id' => $applianceType->id,
            'total_cost' => $request->get('cost'),
            'rate_count' => $request->get('rate'),

        ]);
        return new ApiResource($appliancePerson);
    }

    /**
     * Display the specified resource.
     *
     * @param Person $person
     * @param Request $request
     * @return ApiResource
     */
    public function show(Person $person, Request $request): ApiResource
    {
        $appliances = $this->appliancePerson::with('applianceType', 'rates.logs', 'logs.owner')
            ->where('person_id', $person->id)
            ->get();
        return new ApiResource($appliances);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AppliancePerson $appliancePerson
     * @return Response
     */
    public function update(Request $request, AppliancePerson $appliancePerson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AppliancePerson $appliancePerson
     * @return Response
     */
    public function destroy(AppliancePerson $appliancePerson)
    {
        //
    }
}
