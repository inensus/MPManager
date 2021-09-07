<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Misc\SoldApplianceDataContainer;
use App\Models\AssetPerson;
use App\Models\AssetType;
use App\Models\Person\Person;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        if ($request->get('cost') !== $request->get('preferredPrice')) {
            Log::debug(
                ': Appliance Sale Cost is different than the preferred sale price for appliance ',
                [
                    'user' => $request->get('creatorId'),
                    'customer' => $person->id,
                    'sale_price' => $request->get('cost'),
                    'preferred_price' => $request->get('preferredPrice'),
                    'appliance_type' => $assetType->id,
                    'date_time' => Carbon::now()
                ]
            );
        }
        $assetPerson = $this->assetPerson::query()->create(
            [
            'person_id' => $person->id,
            'asset_type_id' => $assetType->id,
            'total_cost' => $request->get('cost'),
            'down_payment' => $request->get('downPayment'),
            'rate_count' => $request->get('rate'),
            'creator_id' => $request->get('creatorId')

            ]
        );
        $soldApplianceDataContainer = app()->makeWith(
            'App\Misc\SoldApplianceDataContainer',
            [
                'assetType' => $assetType,
                'assetPerson' => $assetPerson,
                'transaction' => null
            ]
        );
        event('appliance.sold', $soldApplianceDataContainer);
        return new ApiResource($assetPerson);
    }

    /**
     * Display the specified resource.
     *
     * @param  Person  $person
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Person $person, Request $request): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('person_id', $person->id)
            ->get();
        return new ApiResource($assets);
    }

    public function show($applianceId): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('id', '=', $applianceId)
            ->get();
        return new ApiResource($assets);
    }
}
