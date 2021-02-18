<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\ApiResource;

class CountryController extends Controller
{
    public function index(): ApiResource
    {
       return ApiResource::make(
            Country::paginate(
                config()->get('services.pagination')
            )
        );
    }

    public function show(Country $country): ApiResource
    {
       return ApiResource::make(
            $country
        );
    }

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
