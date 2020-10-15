<?php


namespace App\Http\Controllers;


use App\Http\Requests\CreateConnectionGroupRequest;
use App\Http\Resources\ApiResource;
use App\Models\ConnectionGroup;

/**
 * @group Connection Group
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
     * List
     * List of all Connection Groups,
     * The list is paginated and each page contains 15 results
     * @return ApiResource
     */

    public function index()
    {

        $connectionGroups = $this->connectionGroup->get();

        return new ApiResource($connectionGroups);
    }

    /**
     * Create
     * Create a new Connection Group
     * @bodyParam name string required
     * @param CreateConnectionGroupRequest $request
     * @return ApiResource
     */

    public function store(CreateConnectionGroupRequest $request)
    {
        $name = $request->input('name');

        $this->connectionGroup->name = $name;
        $this->connectionGroup->save();

        return new ApiResource($this->connectionGroup);

    }

    /**
     * Update
     * Update of the specified Connection Group
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
