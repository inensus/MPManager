<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\SubConnectionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SubConnectionTypeController extends Controller
{

    /**
     * @var SubConnectionType
     */
    private $subConnectionType;

    public function __construct(SubConnectionType $subConnectionType)
    {
        $this->subConnectionType = $subConnectionType;
    }

    /**
     * Display a listing of the resource.
     *
     * @return ApiResource
     */
    public function index()
    {
        if (request()->input('paginate') === null) {
            $connectionTypes = $this->subConnectionType->newQuery()->paginate(15);
        } else {
            $connectionTypes = $this->subConnectionType->newQuery()->get();
        }
        return new ApiResource($connectionTypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param SubConnectionType $subConnectionType
     * @return Response
     */
    public function show(SubConnectionType $subConnectionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SubConnectionType $subConnectionType
     * @return Response
     */
    public function edit(SubConnectionType $subConnectionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param SubConnectionType $subConnectionType
     * @return Response
     */
    public function update(Request $request, SubConnectionType $subConnectionType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SubConnectionType $subConnectionType
     * @return Response
     */
    public function destroy(SubConnectionType $subConnectionType)
    {
        //
    }
}
