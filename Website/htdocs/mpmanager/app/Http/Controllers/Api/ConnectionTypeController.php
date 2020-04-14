<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ConnectionType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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


        return new ApiResource($connectionTypes);
    }

    public function store(Request $request)
    {
        $connectionName = $request->get('name');

        $connectionType = $this->connectionType->create();
        $connectionType->name = $connectionName;
        $connectionType->save();

        return new ApiResource($connectionType);

    }


}
