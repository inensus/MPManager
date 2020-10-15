<?php

namespace App\Http\Controllers;

use App\Models\MiniGrid;
use App\Models\City;
use App\Models\Country;
use App\Http\Requests\CityRequest;
use App\Http\Resources\ApiResource;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group City
 * Class CityController
 * @package App\Http\Controllers
 */
class CityController extends Controller
{
    /**
     * @var City
     */
    private $city;
    /**
     * @var MiniGrid
     */
    private $miniGrid;

    public function __construct(City $city, MiniGrid $miniGrid)
    {
        $this->city = $city;
        $this->miniGrid = $miniGrid;
    }

    /**
     * List
     * List of all cities
     *
     * @return ApiResource
     */
    public function index()
    {
        return new ApiResource(
            $this->city->get()
        );
    }

    /**
     * Detail
     * Details of requested city
     * @urlParam id int required
     * @param $id
     *
     * @return ApiResource
     */
    public function show($id)
    {
        $relation = request('relation');
        if ($relation) {
            return new ApiResource(
                $this->city::with('location', 'country')->findOrFail($id)
            );
        }
        return new ApiResource(
            $this->city->findOrFail($id)
        );
    }

    /**
     * Update
     * Updates the given city
     * @bodyParam name string required
     * @bodyParam mini_grid_id int required
     * @param CityRequest $request
     * @param City $city
     *
     * @return ApiResource
     */
    public function update(CityRequest $request, City $city)
    {
        $country = Country::where('country_code', 'TR')->first();
        $city->name = request('name');
        $city = $country->cities()->save($city);
        return new ApiResource($city);
    }

    /**
     * Create
     * Create a new City
     * @bodyParam name string required
     * @bodyParam mini_grid_id int required
     * @param Request $request
     * @return ApiResource
     * @throws ValidationException
     */
    public function store(CityRequest $request)
    {

        $miniGrid = $this->miniGrid->find($request->input('mini_grid_id'));

        $this->city->name = request('name');
        $this->city->miniGrid()->associate($miniGrid);
        $this->city->save();
        //$this->city->country()->associate($country);
        //$country->cities()->save($this->city);

        //save and return object
        return
            new ApiResource($this->city);
    }
}
