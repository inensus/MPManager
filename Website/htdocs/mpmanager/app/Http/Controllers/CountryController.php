<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\ApiResource;

/**
 * @group Country
 * Class CountryController
 * @package App\Http\Controllers
 */

class CountryController extends Controller
{
    public function index(): ApiResource
    {
        return new ApiResource(
            Country::paginate(
                config()->get('services.pagination')
            )
        );
    }

    /**
     * Detail
     * Details of specified country.
     * @urlParam countryId required.
     * @param Country $country
     * @return ApiResource
     */
    public function show(Country $country): ApiResource
    {
        return new ApiResource(
            $country
        );
    }

    /**
     * Create
     * Create a new country.
     * @bodyParam country_name string required
     * @bodyParam country_code int required
     * @param CountryRequest $request
     * @return ApiResource
     */
    public function store(CountryRequest $request): ApiResource
    {
        return
            new ApiResource(
                Country::create(
                    request()->only(
                        [
                        'country_name',
                        'country_code',
                        ]
                    )
                )
            );
    }
}
