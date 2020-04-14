<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Http\Requests\CityRequest;
use App\Http\Resources\ApiResource;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class CityController extends Controller
{
    /**
     * @var City
     */
    private $city;


    public function __construct(City $city)
    {
        $this->city = $city;
    }

    /**
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
     * Details of requested city
     *
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
     * Updates the given city
     *
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
     * @param Request $request
     *
     * @return ApiResource
     * @throws ValidationException
     */
    public function store(CityRequest $request)
    {
        $validation = Validator::make($request->all(), City::$rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }
        $country = Country::where('country_code', request('country'))->first();


        $this->city->name = request('name');
        $this->city->save();
        $this->city->country()->associate($country);
        //$country->cities()->save($this->city);

        //save and return object
        return
            new ApiResource($this->city);
    }
}
