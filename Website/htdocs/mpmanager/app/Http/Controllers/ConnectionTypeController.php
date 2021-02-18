<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ConnectionType;
use Illuminate\Http\Request;

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

    public function index(Request $request): ApiResource
    {
        if ($request->get('paginate') === null) {
            $connectionTypes = $this->connectionType->paginate(15);
        } else {
            $connectionTypes = $this->connectionType->get();
        }
       return ApiResource::make($connectionTypes);
    }


    public function store(Request $request): ApiResource
    {
        $connectionName = $request->get('name');
        $connectionType = $this->connectionType->create();
        $connectionType->name = $connectionName;
        $connectionType->save();
       return ApiResource::make($connectionType);
    }

    public function show($connectionTypeId): ApiResource
    {
        $meter_count_relation = request()->input('meter_count');
        if ($connectionTypeId !== null) {
            $connectionTypeDetail = $this->connectionType->newQuery();
            if ($meter_count_relation) {
                $connectionTypeDetail = $connectionTypeDetail->withCount('meterParameters');
            }
            $connectionTypeDetail = $connectionTypeDetail->where('id', $connectionTypeId)->get();
           return ApiResource::make($connectionTypeDetail);
        }
       return ApiResource::make(null);
    }

    public function update(ConnectionType $connectionType): ApiResource
    {
        $connectionType->name = request('name');
        $connectionType->save();
       return ApiResource::make($connectionType);
    }
}
