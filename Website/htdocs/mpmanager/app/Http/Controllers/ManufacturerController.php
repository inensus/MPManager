<?php

namespace App\Http\Controllers;

use App\Models\Address\Address;
use App\Http\Requests\ManufacturerRequest;
use App\Http\Resources\ApiResource;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group   Manufacturer
 * Class ManufacturerController
 * @package App\Http\Controllers
 */
class ManufacturerController extends Controller
{
    /**
     * List of all Manufacturers
     * @responseFile responses/manufacturer/manufacturers.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(
            Manufacturer::paginate(15)
        );
    }


    /**
     * Create a new manufacturer.
     *
     * @bodyParam name string required
     * @bodyParam contact_person string required
     * @bodyParam website string required
     * @bodyParam api_name string required
     * @bodyParam city_id int required
     * @bodyParam email string required
     * @bodyParam phone string required
     * @bodyParam street string required
     * @param  ManufacturerRequest $request
     * @return ApiResource
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
     * Details of specified manufacturer.
     * @urlParam manufacturerId required.
     * @responseFile responses/manufacturer/manufacturer.detail.json
     * @param  Manufacturer $manufacturer
     * @return ApiResource
     */
    public function show(Manufacturer $manufacturer)
    {
        return new ApiResource(
            $manufacturer::with('address.city.country')->get()
        );
    }
}
