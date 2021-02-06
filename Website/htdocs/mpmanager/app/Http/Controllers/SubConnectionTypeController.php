<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Http\Requests\SubConnectionTypeCreateRequest;
use App\Models\SubConnectionType;
use App\Models\Meter\MeterTariff;
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
    public function index($connectionTypeId = null)
    {
        $connectionTypes = $this->subConnectionType::with('tariff')->newQuery();
        if ($connectionTypeId !== null) {
            $connectionTypes->where('connection_type_id', $connectionTypeId);
        }
        if (request()->input('paginate') !== null) {
            $connectionTypes = $connectionTypes->paginate(15);
        } else {
            $connectionTypes = $connectionTypes->get();
        }
        return new ApiResource($connectionTypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  SubConnectionTypeCreateRequest $request
     * @return ApiResource
     */

    public function store(SubConnectionTypeCreateRequest $request): ApiResource
    {
        $subConnectionType = $this->subConnectionType::query()
            ->create(
                $request->only(['name', 'connection_type_id', 'tariff_id'])
            );
        return new ApiResource($subConnectionType);
    }

    /**
     * Display the specified resource.
     *
     * @param  SubConnectionType $subConnectionType
     * @return Response
     */
    public function show(SubConnectionType $subConnectionType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SubConnectionType $subConnectionType
     * @return Response
     */
    public function edit(SubConnectionType $subConnectionType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  SubConnectionType $subConnectionType
     * @return Response
     */
    public function update(SubConnectionType $subConnectionType): ApiResource
    {
        $subConnectionType->update(request()->only(['name','tariff_id']));
        $subConnectionType->fresh();
        $subConnectionType->load('tariff');
        return new ApiResource($subConnectionType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SubConnectionType $subConnectionType
     * @return Response
     */
    public function destroy(SubConnectionType $subConnectionType)
    {
        //
    }
}
