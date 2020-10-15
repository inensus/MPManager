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

/**
 * @group Manufacturers
 * Class ManufacturerController
 * @package App\Http\Controllers
 */
class ManufacturerController extends Controller
{
    /**
     * List
     * List of all manufacturers.
     *
     */
    public function index()
    {
        return new ApiResource(
            Manufacturer::paginate(15)
        );
    }


    /**
     * Create
     * Create a new manufacturer.
     * @bodyParam name string required
     * @bodyParam phone string
     * @bodyParam email string
     * @bodyParam contact_person string
     * @bodyParam website string
     * @bodyParam city_id int
     * @bodyParam address string
     * @bodyParam api_name string
     * @bodyParam master_key int required
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
     * Detail
     * Detail of the specified Manufacturer.
     * @urlParam id int required
     * @param  Manufacturer $manufacturer
     * @return ApiResource
     */
    public function show(Manufacturer $manufacturer)
    {
        return new ApiResource(
            $manufacturer::with('address.city.country')->get()
        );
    }
    public function update(Request $request, Manufacturer $manufacturer)
    {
        //
    }

    public function destroy(Manufacturer $manufacturer)
    {
        //
    }
}
