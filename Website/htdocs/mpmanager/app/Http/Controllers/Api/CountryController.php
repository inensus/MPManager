<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group Countries
 * Class CountryController
 * @package App\Http\Controllers
 */
class CountryController extends Controller
{
    /**
     * List
     * List of all countries
     * @return ApiResource
     */
    public function index()
    {
        return new ApiResource(
            Country::paginate(
                config()->get('services.pagination')
            )
        );
    }

    /**
     * Detail
     * Detail of the specified country.
     * @urlParam country_code int required
     * @param Country $country
     * @return ApiResource
     */

    public function show(Country $country)
    {
        return new ApiResource(
            $country
        );
    }

    /**
     * Create
     * Creata a new country
     * @bodyParam country_name string required
     * @bodyParam country_code int required
     * @param CountryRequest $request
     * @return ApiResource
     */

    public function store(CountryRequest $request)
    {
        return
            new ApiResource(
                Country::create(
                    request()->only([
                        'country_name',
                        'country_code',
                    ])
                )
            );
    }
}
