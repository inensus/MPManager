<?php


namespace App\Http\Controllers;

use App\Http\Requests\CreateConnectionGroupRequest;
use App\Http\Resources\ApiResource;
use App\Models\ConnectionGroup;

class ConnectionGroupController
{
    /**
     * @var ConnectionGroup
     */
    private $connectionGroup;

    public function __construct(ConnectionGroup $connectionGroup)
    {
        $this->connectionGroup = $connectionGroup;
    }

    public function index()
    {

        $connectionGroups = $this->connectionGroup->get();

        return new ApiResource($connectionGroups);
    }

    public function store(CreateConnectionGroupRequest $request)
    {
        $name = $request->input('name');

        $this->connectionGroup->name = $name;
        $this->connectionGroup->save();

        return new ApiResource($this->connectionGroup);
    }

    public function update(ConnectionGroup $connectionGroup): ApiResource
    {
        $connectionGroup->update(request()->only(['name']));
        $connectionGroup->fresh();
        return new ApiResource($connectionGroup);
    }
}
