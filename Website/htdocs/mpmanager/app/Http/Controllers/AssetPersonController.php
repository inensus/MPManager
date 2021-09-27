<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetPerson;
use App\Models\AssetType;
use App\Models\Person\Person;
use App\Services\AppliancePersonService;
use Illuminate\Http\Request;
use Ramsey\Collection\Collection;

/**
 * @group   Appliance Person
 * Class AssetPersonController
 * @package App\Http\Controllers
 */

class AssetPersonController extends Controller
{

    /**
     * @var AssetPerson
     */
    private $assetPerson;

    private $assetPersonService;


    public function __construct(
        AssetPerson $assetPerson,
        AppliancePersonService $assetPersonService,
    ) {
        $this->assetPerson = $assetPerson;
        $this->assetPersonService = $assetPersonService;
    }

    /**
     * Store sold appliance
     * @urlParam applianceId required
     * @urlParam personId required
     *
     * @bodyParam asset_type_id int required
     * @bodyParam total_cost float required
     * @bodyParam down_payment float
     * @bodyParam rate_count int required
     * @bodyParam creator_id int required
     * @param  AssetType $assetType
     * @param  Person    $person
     * @param  Request   $request
     * @return ApiResource
     */
    public function store(
        AssetType $assetType,
        Person $person,
        Request $request
    ): ApiResource {

        $assetPerson = $this->assetPersonService->createFromRequest($request, $person, $assetType);
        return new ApiResource($assetPerson);
    }

    /**
     * List Sold Appliances
     * A list of the all sold appliances for the given personId.
     * @responseFile responses/appliance/appliance.person.sold.list.json
     * @urlParam personId required
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

    /**
     * Detail Sold Appliance
     * Detail of sold appliance with logs and rates for the given applianceId.
     * @urlParam applianceId required
     * @responseFile responses/appliance/appliance.person.detail.json
     * @param $applianceId
     * @return ApiResource
     */
    public function show($applianceId): ApiResource
    {
        $appliance = $this->assetPersonService->getApplianceDetails($applianceId);

        return new ApiResource($appliance);
    }
}
