<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetPerson;
use App\Models\AssetType;
use App\Models\Person\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  AssetType $assetType
     * @param  Person    $person
     * @param  Request   $request
     * @return ApiResource
     */
    public function store(AssetType $assetType, Person $person, Request $request): ApiResource
    {
        $assetPerson = $this->assetPerson::create(
            [
            'person_id' => $person->id,
            'asset_type_id' => $assetType->id,
            'total_cost' => $request->get('cost'),
            'rate_count' => $request->get('rate'),

            ]
        );
       return ApiResource::make($assetPerson);
    }

    /**
     * Display the specified resource.
     *
     * @param  Person  $person
     * @param  Request $request
     * @return ApiResource
     */
    public function show(Person $person, Request $request): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('person_id', $person->id)
            ->get();
       return ApiResource::make($assets);
    }
}
