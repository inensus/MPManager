<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplianceTypeRequest;
use App\Http\Resources\ApiResource;
use App\Models\ApplianceType;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Inensus\Ticket\Trello\Api;

class ApplianceTypeController extends Controller
{

    use SoftDeletes;
    /**
     * @var ApplianceType
     */
    private $applianceType;

    public function __construct(ApplianceType $applianceType)
    {
        $this->applianceType = $applianceType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(
            $this->applianceType->paginate(15)
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ApplianceTypeRequest $request
     * @return ApiResource
     */
    public function store(ApplianceTypeRequest $request): ApiResource
    {
        $name = $request->get('appliance_type_name');
        $appliance = $this->applianceType::create([
            'name' => $name
        ]);

        return new ApiResource($appliance);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ApplianceType $applianceType
     * @return ApiResource
     */
    public function update(ApplianceTypeRequest $request, ApplianceType $applianceType)
    {
        $applianceType->name = $request->get('appliance_type_name');
        $applianceType->save();
        return new ApiResource($applianceType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApplianceType $applianceType
     * @return ApiResource
     * @throws Exception
     */
    public function destroy(ApplianceType $applianceType): ApiResource
    {
        $applianceType->delete();
        return new ApiResource($applianceType);
    }
}
