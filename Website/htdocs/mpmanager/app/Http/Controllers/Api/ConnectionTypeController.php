<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ConnectionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Connection Types
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
     * List
     * List of all Connection Types
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
     * Create
     * Create a new connection type
     * @bodyParam name string required
     * @param Request $request
     * @return ApiResource
     */
    public function store(Request $request)
    {
        $connectionName = $request->get('name');

        $connectionType = $this->connectionType->create();
        $connectionType->name = $connectionName;
        $connectionType->save();

        return new ApiResource($connectionType);

    }

    /**
     * Detail
     * Detail of the specified Connection Type
     * @urlParam connectionTypeId int required
     * @param $connectionTypeId
     * @return ApiResource
     */

    public function show($connectionTypeId)
    {
        $meter_count_relation = request()->input('meter_count');
        if($connectionTypeId !== null){
            $connectionTypeDetail = $this->connectionType->newQuery();
            if($meter_count_relation){
                $connectionTypeDetail = $connectionTypeDetail->withCount('meterParameters');
            }
            $connectionTypeDetail = $connectionTypeDetail->where('id', $connectionTypeId)->get();
            return new ApiResource($connectionTypeDetail);
        }


    }

    /**
     * Update
     * Update of the specified Connection Type
     * @bodyParam name string required
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
