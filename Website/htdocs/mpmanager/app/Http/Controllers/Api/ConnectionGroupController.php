<?php


namespace App\Http\Controllers;


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
}
