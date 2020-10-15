<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetTypeCreateRequest;
use App\Http\Requests\AssetTypeUpdateRequest;
use App\Http\Resources\ApiResource;
use App\Models\AssetType;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @group Appliance Types
 * Class AssetTypeController
 * @package App\Http\Controllers
 */
class AssetTypeController extends Controller
{

    use SoftDeletes;

    /**
     * @var AssetType
     */
    private $assetType;

    public function __construct(AssetType $assetType)
    {
        $this->assetType = $assetType;
    }

    /**
     * List
     * List of all Asset Types
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(
            $this->assetType->newQuery()->paginate(15)
        );
    }


    /**
     * Create
     * Create a new Asset Type
     * @bodyParam name string required
     * @bodyParam price int required
     * @param AssetTypeCreateRequest $request
     * @return ApiResource
     */
    public function store(AssetTypeCreateRequest $request): ApiResource
    {
        $asset = $this->assetType::query()
            ->create(
                $request->only(['name', 'price'])
            );
        return new ApiResource($asset);
    }


    /**
     * Update
     * Update the specified Asset Type
     * @bodyParam asset_type_id int required
     * @bodyParam name string required
     * @bodyParam price int required
     * @param AssetTypeUpdateRequest $request
     * @param AssetType $assetType
     * @return ApiResource
     */
    public function update(AssetTypeUpdateRequest $request, AssetType $assetType): ApiResource
    {
        $assetType->update($request->only(['name', 'price']));
        return new ApiResource($assetType);
    }

    /**
     * Remove
     * Remove the specified asset type.
     * @bodyParam asset_type_id int required
     * @param AssetType $assetType
     * @return ApiResource
     * @throws Exception
     */
    public function destroy(AssetType $assetType): ApiResource
    {
        $assetType->delete();
        return new ApiResource($assetType);
    }
}
