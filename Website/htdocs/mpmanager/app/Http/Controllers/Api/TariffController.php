<?php

namespace App\Http\Controllers;


use App\Http\Requests\TariffCreateRequest;
use App\Http\Resources\ApiResource;
use App\Models\Meter\MeterTariff;

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
     * @param TariffCreateRequest $request
     * @return ApiResource
     */
    public function store(TariffCreateRequest $request): ApiResource
    {
        $tariff = MeterTariff::create(
            [
                'name' => $request->input('name'),
                'factor' => $request->input('factor'),
                'currency' => $request->input('currency'),
                'price' => $request->input('price'),
                'total_price' => $request->input('price'),
            ]);

        return new ApiResource($tariff);
    }

}
