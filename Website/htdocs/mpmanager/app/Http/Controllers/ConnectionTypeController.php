<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ConnectionType;
use Illuminate\Http\Request;

/**
 * @group   Connection
 * Class ConnectionTypeController
 * @package App\Http\Controllers
 */

class ConnectionTypeController extends Controller
{


    /**
     * @var ConnectionType
     */
    private $connectionType;

    public function __construct(ConnectionType $connectionType)
    {
        $this->connectionType = $connectionType;
    }

    /**
     * Connection Type List
     * A List of all connection types.
     * @responseFile responses/connection/types.list.json
     * @param Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {
        if ($request->get('paginate') === null) {
            $connectionTypes = $this->connectionType->paginate(15);
        } else {
            $connectionTypes = $this->connectionType->get();
        }
        return new ApiResource($connectionTypes);
    }

    /**
     * Connection Type Create
     * Create a new connection type.
     * @bodyParam name string required
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request): ApiResource
    {
        $connectionName = $request->get('name');
        $connectionType = $this->connectionType->create();
        $connectionType->name = $connectionName;
        $connectionType->save();
        return new ApiResource($connectionType);
    }

    /**
     * Connection Type Detail
     * Details of specified connection type.
     * @urlParam connectionTypeId required.
     * @responseFile responses/connection/type.detail.json
     * @param $connectionTypeId
     * @return ApiResource
     */
    public function show($connectionTypeId): ApiResource
    {
        $meter_count_relation = request()->input('meter_count');
        if ($connectionTypeId !== null) {
            $connectionTypeDetail = $this->connectionType->newQuery();
            if ($meter_count_relation) {
                $connectionTypeDetail = $connectionTypeDetail->withCount('meterParameters');
            }
            $connectionTypeDetail = $connectionTypeDetail->where('id', $connectionTypeId)->get();
            return new ApiResource($connectionTypeDetail);
        }
        return new ApiResource(null);
    }

    /**
     * Connection Type Update
     * Update of specified connection type.
     * @urlParam connectionTypeId required.
     *
     * @bodyParam name string required.
     * @param ConnectionType $connectionType
     * @return ApiResource
     */
    public function update(ConnectionType $connectionType): ApiResource
    {
        $connectionType->name = request('name');
        $connectionType->save();
        return new ApiResource($connectionType);
    }
}
