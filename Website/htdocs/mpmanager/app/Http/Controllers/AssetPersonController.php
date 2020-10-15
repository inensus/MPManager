<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetPerson;
use App\Models\AssetType;
use App\Models\Person\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Appliance Person
 * Class AssetPersonController
 * @package App\Http\Controllers
 */
class AssetPersonController extends Controller
{

    /**
     * @var AssetPerson
     */
    private $assetPerson;

    public function __construct(AssetPerson $assetPerson)
    {
        $this->assetPerson = $assetPerson;
    }

    public function index()
    {
        //
    }


    /**
     * Create
     * Create a new Asset Person
     * @bodyParam person_id int required
     * @bodyParam asset_type_id int required
     * @bodyParam total_cost int required
     * @bodyParam rate_count int required
     * @param AssetType $assetType
     * @param Person $person
     * @param Request $request
     * @return ApiResource
     */
    public function store(AssetType $assetType, Person $person, Request $request): ApiResource
    {
        $assetPerson = $this->assetPerson::create([
            'person_id' => $person->id,
            'asset_type_id' => $assetType->id,
            'total_cost' => $request->get('cost'),
            'rate_count' => $request->get('rate'),

        ]);
        return new ApiResource($assetPerson);
    }

    /**
     * Detail
     * Detail of the specified person asset
     * @urlParam person_id int required
     * @param Person $person
     * @param Request $request
     * @return ApiResource
     */
    public function show(Person $person, Request $request): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('person_id', $person->id)
            ->get();
        return new ApiResource($assets);
    }


    public function update(Request $request, AssetPerson $assetPerson)
    {
        //
    }

    public function destroy(AssetPerson $assetPerson)
    {
        //
    }
}
