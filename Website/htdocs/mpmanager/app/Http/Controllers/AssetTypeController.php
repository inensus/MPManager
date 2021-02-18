<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetTypeCreateRequest;
use App\Http\Requests\AssetTypeUpdateRequest;
use App\Http\Resources\ApiResource;
use App\Models\AssetType;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
       return ApiResource::make(
            $this->assetType->newQuery()->paginate(15)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  AssetTypeCreateRequest $request
     * @return ApiResource
     */
    public function store(AssetTypeCreateRequest $request): ApiResource
    {
        $asset = $this->assetType::query()
            ->create(
                $request->only(['name', 'price'])
            );
       return ApiResource::make($asset);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  AssetTypeUpdateRequest $request
     * @param  AssetType              $assetType
     * @return ApiResource
     */
    public function update(AssetTypeUpdateRequest $request, AssetType $assetType): ApiResource
    {
        $assetType->update($request->only(['name', 'price']));
       return ApiResource::make($assetType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  AssetType $assetType
     * @return ApiResource
     * @throws Exception
     */
    public function destroy(AssetType $assetType): ApiResource
    {
        $assetType->delete();
       return ApiResource::make($assetType);
    }
}
