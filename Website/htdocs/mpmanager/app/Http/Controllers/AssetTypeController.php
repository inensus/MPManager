<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetTypeRequest;
use App\Http\Resources\ApiResource;
use App\Models\AssetType;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Inensus\Ticket\Trello\Api;

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
        return new ApiResource(
            $this->assetType->paginate(15)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param AssetTypeRequest $request
     * @return ApiResource
     */
    public function store(AssetTypeRequest $request): ApiResource
    {
        $name = $request->get('asset_type_name');
        $asset = $this->assetType::create([
            'name' => $name
        ]);

        return new ApiResource($asset);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AssetType $assetType
     * @return ApiResource
     */
    public function update(AssetTypeRequest $request, AssetType $assetType)
    {
        $assetType->name = $request->get('asset_type_name');
        $assetType->save();
        return new ApiResource($assetType);
    }

    /**
     * Remove the specified resource from storage.
     *
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
