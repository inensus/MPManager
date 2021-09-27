<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetTypeCreateRequest;
use App\Http\Requests\AssetTypeUpdateRequest;
use App\Http\Resources\ApiResource;
use App\Models\AssetType;
use App\Services\ApplianceTypeService;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
 * @group   Appliance Type
 * Class AssetTypeController
 * @package App\Http\Controllers
 */

class AssetTypeController extends Controller
{
    use SoftDeletes;

    /**
     * @var AssetType
     */
    private $applianceTypeService;

    public function __construct(ApplianceTypeService $applianceTypeService)
    {
        $this->applianceTypeService = $applianceTypeService;
    }

    /**
     * List Appliance Types
     * A list of the all appliance types.
     * @responseFile responses/appliance/appliance.types.json
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {
        return new ApiResource($this->applianceTypeService->getApplianceTypes($request));
    }


    /**
     * Create Appliance Type
     * Store a new appliance type.
     *
     * @bodyParam name string required
     * @bodyParam price float required
     * @param  AssetTypeCreateRequest $request
     * @return ApiResource
     */
    public function store(AssetTypeCreateRequest $request): ApiResource
    {
        $appliance = $this->applianceTypeService->createApplianceType($request);
        return new ApiResource($appliance);
    }


    /**
     * Update Appliance Type
     * Update the specified appliance in storage.
     *
     * @urlParam applianceId required
     * @bodyParam name string
     * @bodyParam price float
     * @param  AssetTypeUpdateRequest $request
     * @param  AssetType              $assetType
     * @return ApiResource
     */
    public function update(AssetTypeUpdateRequest $request, AssetType $assetType): ApiResource
    {
        $appliance = $this->applianceTypeService->updateApplianceType($request, $assetType);
        return new ApiResource($appliance);
    }

    /**
     * Delete Appliance Type
     * Remove the specified appliance from storage.
     *
     * @urlParam applianceId required
     * @param  AssetType $assetType
     * @return ApiResource
     * @throws Exception
     */
    public function destroy(AssetType $assetType): ApiResource
    {
        return new ApiResource(
            $this->applianceTypeService->deleteApplianceType($assetType)
        );
    }
}
