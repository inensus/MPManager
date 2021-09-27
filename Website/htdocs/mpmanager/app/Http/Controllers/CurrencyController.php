<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use Storage;

/**
 * @group   Currency
 * Class CurrencyController
 * @package App\Http\Controllers
 */

class CurrencyController extends Controller
{
    private $currencyList;

    public function __constructor(CurrencyList $currencyList)
    {
        $this->currencyList = $currencyList;
    }

    /**
     * List of all Currencies
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $currency = Storage::disk('local')->get('currency.json');
        $currency = json_decode($currency, true);
        return new ApiResource($currency);
    }
}
