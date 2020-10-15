<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Http\Requests\SubConnectionTypeCreateRequest;
use App\Models\SubConnectionType;
use App\Models\Meter\MeterTariff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Sub Connection Types
 * Class SubConnectionTypeController
 * @package App\Http\Controllers
 */
class SubConnectionTypeController extends Controller
{

    /**
     * @var SubConnectionType
     */
    private $subConnectionType;

    public function __construct(SubConnectionType $subConnectionType)
    {
        $this->subConnectionType = $subConnectionType;
    }

    /**
     * List
     * Sub Connection Types List of the specified Connection Type
     *
     * @return ApiResource
     */
    public function index($connectionTypeId)
    {
        $connectionTypes = $this->subConnectionType::with('tariff')->newQuery();
        if($connectionTypeId !== null){
            $connectionTypes->where('connection_type_id', $connectionTypeId);
        }
        if (request()->input('paginate') !== null) {
            $connectionTypes = $connectionTypes->paginate(15);
        } else {
            $connectionTypes = $connectionTypes->get();
        }
        return new ApiResource($connectionTypes);
    }

    /**
     * Create
     * Create a new Sub Connection Type of the specified Connection Type.
     * @bodyParam name string required
     * @bodyParam connection_type_id int required
     * @bodyParam tariff_id int required
     * @param SubConnectionTypeCreateRequest $request
     * @return ApiResource
     */
    public function store(SubConnectionTypeCreateRequest $request): ApiResource
    {
        $subConnectionType = $this->subConnectionType::query()
            ->create(
                $request->only(['name', 'connection_type_id', 'tariff_id'])
            );
        return new ApiResource($subConnectionType);
    }

    /**
     * Update
     * Update the specified Sub Connection Type.
     * @bodyParam name string required
     * @bodyParam tariff_id int required
     * @param SubConnectionType $subConnectionType
     * @return Response
     */
    public function update(SubConnectionType $subConnectionType): ApiResource
    {
        $subConnectionType->update(request()->only(['name','tariff_id']));
        $subConnectionType->fresh();
        $subConnectionType->load('tariff');
        return new ApiResource($subConnectionType);
    }

    public function destroy(SubConnectionType $subConnectionType)
    {
        //
    }
}
