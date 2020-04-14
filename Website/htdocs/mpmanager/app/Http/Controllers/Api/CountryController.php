<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CountryController extends Controller
{
    public function index()
    {
        return new ApiResource(
            Country::paginate(
                config()->get('services.pagination')
            )
        );
    }

    public function show(Country $country)
    {
        return new ApiResource(
            $country
        );
    }

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
