<?php

namespace App\Http\Controllers;

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

    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $miniGrids = $this->miniGrid->get();
        return new ApiResource($miniGrids);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @param Request $request
     *
     * @return ApiResource
     */
    public function show($id, Request $request)
    {
        $relation = $request->get('relation');

        if ((int)$relation === 1) {
            $miniGrid = $this->miniGrid::with('location')->find($id);
        } else {
            $miniGrid = $this->miniGrid->find($id);
        }
        return new ApiResource($miniGrid);
    }
}
