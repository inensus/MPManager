<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMiniGridRequest;
use App\Http\Requests\UpdateMiniGridRequest;
use App\Http\Resources\ApiResource;
use App\Models\MiniGrid;
use Illuminate\Http\Request;

/**
 * @group   MiniGrid
 * Class MiniGridController
 * @package App\Http\Controllers
 */
class MiniGridController extends Controller
{

    /**
     * @var MiniGrid
     */
    private $miniGrid;

    public function __construct(MiniGrid $miniGrid)
    {
        $this->miniGrid = $miniGrid;
    }

    public function store(StoreMiniGridRequest $request): ApiResource
    {
        $miniGrid = $this->miniGrid::query()->create($request->only('cluster_id', 'name'));
        return new ApiResource($miniGrid);
    }

    /**
     * List of all MiniGrids
     * A list of the all mini grid data
     * @urlParam data_stream filters the list based on data_stream column
     * @responseFile responses/miniGrid/miniGrids.list.json
     *
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Request $request): ApiResource
    {

        $miniGrids = $this->miniGrid->newQuery();
        if ($request->has('data_stream')) {
            $miniGrids->where('data_stream', $request->input('data_stream'));
        }
        return new ApiResource($miniGrids->get());
    }

    /**
     * Detail of MiniGrid
     * Details of the specified mini grid data.
     * @bodyParam id int required
     * @responseFile responses/miniGrid/miniGrid.detail.json
     * @param int     $id
     *
     * @param Request $request
     *
     * @return ApiResource
     */
    public function show($id, Request $request): ApiResource
    {
        $relation = $request->get('relation');

        if ((int)$relation === 1) {
            $miniGrid = $this->miniGrid::with('location')->find($id);
        } else {
            $miniGrid = $this->miniGrid->find($id);
        }

        return new ApiResource($miniGrid);
    }

    /**
     * Update Mini Grid
     * Update of the specified mini grid.
     * @urlParam miniGridId required.
     * @bodyParam name string The name of the MiniGrid.
     * @bodyParam data_stream int If the data_stream is enabled or not.
     *
     * @param  MiniGrid              $miniGrid
     * @param  UpdateMiniGridRequest $request
     * @return ApiResource
     */
    public function update(MiniGrid $miniGrid, UpdateMiniGridRequest $request): ApiResource
    {
        $miniGrid->name = $request->input('name') ?? $miniGrid->name;
        $miniGrid->data_stream = $request->input('data_stream') ?? $miniGrid->data_stream;
        $miniGrid->save();
        return new ApiResource($miniGrid);
    }
}
