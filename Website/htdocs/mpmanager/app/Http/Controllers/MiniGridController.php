<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMiniGridRequest;
use App\Http\Resources\ApiResource;
use App\Models\MiniGrid;
use Illuminate\Http\Request;

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

    public function store(StoreMiniGridRequest $request)
    {
        $miniGrid = $this->miniGrid::create($request->all());

        return new ApiResource($miniGrid);
    }

    /**
     * List
     * @urlParam data_stream filters the list based on data_stream column
     *
     * @param Request $request
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
     * Detail
     * @bodyParam id int required
     *
     * @param int $id
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
     * Update
     * @bodyParam name string The name of the MiniGrid.
     * @bodyParam data_stream int If the data_stream is enabled or not.
     *
     * @param MiniGrid $miniGrid
     * @param Request $request
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
