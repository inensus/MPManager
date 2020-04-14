<?php

namespace App\Http\Controllers;

use App\Models\Address\Address;
use App\Exceptions\ValidationException;
use App\Http\Requests\ManufacturerRequest;
use App\Http\Resources\ApiResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ManufacturerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return new ApiResource(
            Manufacturer::paginate(15)
        );
    }


    /**
     * Store a newly created resource in storage.
     * @param ManufacturerRequest $request
     *@return ApiResource
     */
    public function store(ManufacturerRequest $request)
    {
        $manufacturer = new Manufacturer();
        $manufacturer->name = request('name');
        $manufacturer->contact_person = request('contact_person');
        $manufacturer->website = request('website');
        $manufacturer->api_name = request('api_name');

        $address = new Address();
        $address->city_id = request('city_id');
        $address->email = request('email');
        $address->phone = request('phone');
        $address->street = request('street');

        $manufacturer->save();
        $manufacturer->address()->save($address);

        return
            new ApiResource(
                $manufacturer
            );
    }

    /**
     * Display the specified resource.
     * @param  Manufacturer $manufacturer
     * @return ApiResource
     */
    public function show(Manufacturer $manufacturer)
    {
        return new ApiResource(
            $manufacturer::with('address.city.country')->get()
        );
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Manufacturer $manufacturer
     * @return Response
     */
    public function update(Request $request, Manufacturer $manufacturer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Manufacturer $manufacturer
     * @return Response
     */
    public function destroy(Manufacturer $manufacturer)
    {
        //
    }
}
