<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use Storage;


/**
 * @group   Country
 * Class CountryListController
 * @package App\Http\Controllers
 */

class CountryListController extends Controller
{
    private $countryList;

    public function __constructor(CountryList $countryList)
    {
        $this->countryList = $countryList;
    }

    /**
     * List of all Countries
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $country = Storage::disk('local')->get('countries.json');
        $country = json_decode($country, true);
        return new ApiResource($country);
    }
}
