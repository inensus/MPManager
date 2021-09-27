<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConnectionGroupRequest;
use App\Http\Resources\ApiResource;
use App\Models\ConnectionGroup;

/**
 * @group   Connection
 * Class ConnectionGroupController
 * @package App\Http\Controllers
 */
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

    /**
     * Connection Group List
     * A list of the all connection groups.
     * @responseFile responses/connection/groups.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->connectionGroup->get());
    }

    /**
     * Connection Group Create
     * Create a new connection group.
     *
     * @bodyParam name string required.
     * @param CreateConnectionGroupRequest $request
     * @return ApiResource
     */
    public function store(CreateConnectionGroupRequest $request): ApiResource
    {
        $name = $request->input('name');
        $this->connectionGroup->name = $name;
        $this->connectionGroup->save();
        return new ApiResource($this->connectionGroup);
    }

    /**
     * Connection Group Update
     * Update of the specified connection group.
     * @urlParam connectionGroupId required
     *
     * @bodyParam name string required
     * @param ConnectionGroup $connectionGroup
     * @return ApiResource
     */
    public function update(ConnectionGroup $connectionGroup): ApiResource
    {
        $connectionGroup->update(request()->only(['name']));
        $connectionGroup->fresh();
        return new ApiResource($connectionGroup);
    }
}
