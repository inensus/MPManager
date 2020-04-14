<?php

namespace App\Http\Controllers;


use App\Http\Requests\TariffRequest;
use App\Http\Resources\ApiResource;

use App\Models\Meter\MeterTariff;
use Illuminate\Http\Request;

/**
 * @group Tariffs
 * Class TariffController
 * @package App\Http\Controllers
 */
class TariffController extends Controller
{
    /**
     * List
     * a list of all tariffs.
     * The list is paginated and each page contains 15 results
     * @responseFile responses/tariffs/tariffs.list.json
     * @return ApiResource
     */
    public function index()
    {
        return new ApiResource(
            MeterTariff::with('accessRate')->paginate(15)
        );
    }

    /**
     * Detail
     * @urlParam id int required
     * @responseFile responses/tariffs/tariff.detail.json
     * @param MeterTariff $tariff
     * @return ApiResource
     */
    public function show(MeterTariff $tariff)
    {
        return new ApiResource(
            $tariff
        );
    }

    /**
     * Create
     * @bodyParam name string required
     * @bodyParam factor int. The factor between two different sub tariffs. Like day/night sub-tariffs.
     * @bodyParam currency string
     * @bodyParam price int required The price is;  wanted-kWh-price  X 100 . The last two digits are basically the amount after  comma.
     * @param TariffRequest $request
     * @return ApiResource
     */
    public function store(TariffRequest $request)
    {
        $tariff = new MeterTariff();
        $tariff->name = request('name', '');
        $tariff->factor = request('factor', null);
        $tariff->currency = request('currency', '');
        $tariff->price = request('price');
        $tariff->save();
        //append access-rate for the output
        $tariff->access_rate = $tariff->accessRate()->create([
            'amount' => request('access_rate_amount'),
            'period' => request('access_rate_period'),
        ]);


        return new ApiResource(
            $tariff
        );
    }

}
