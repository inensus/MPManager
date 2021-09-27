<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffCreateRequest;
use App\Http\Resources\ApiResource;
use App\Http\Services\MeterTariffService;
use App\Models\Meter\MeterTariff;

/**
 * @group   Tariffs
 * Class TariffController
 * @package App\Http\Controllers
 */
class TariffController extends Controller
{


    private $meterTariffService;

    public function __construct(MeterTariffService $meterTariffService)
    {
        $this->meterTariffService = $meterTariffService;
    }

    /**
     * List of all tariffs.
     * a list of all tariffs.
     * The list is paginated and each page contains 15 results
     *
     * @responseFile responses/tariffs/tariffs.list.json
     * @return       ApiResource
     */
    public function index()
    {
        return new ApiResource(
            MeterTariff::with(
                [
                    'accessRate',
                    'pricingComponent',
                    'socialTariff',
                    'tou'
                ]
            )->paginate(15)
        );
    }

    /**
     * Details of the specified tariff.
     * @urlParam     id int required
     * @responseFile responses/tariffs/tariff.detail.json
     * @param MeterTariff $tariff
     * @return       ApiResource
     */
    public function show(MeterTariff $tariff)
    {

        $meterTariff = MeterTariff::with(
            [
                'accessRate',
                'pricingComponent',
                'socialTariff',
                'tou'
            ]
        )->where('id', $tariff->id)->first();

        return new ApiResource(
            $meterTariff
        );
    }

    /**
     * Create a new tariff.
     * @bodyParam name string required
     * @bodyParam factor int. The factor between two different sub tariffs. Like day/night sub-tariffs.
     * @bodyParam currency string
     * @bodyParam price int required. kWh-price X 100 . The last two digits are basically the amount after comma.
     * @param TariffCreateRequest $request
     * @return ApiResource
     */
    public function store(TariffCreateRequest $request)
    {
        $newTariff = MeterTariff::query()
            ->create(
                [
                    'name' => $request->input('name'),
                    'factor' => $request->input('factor'),
                    'currency' => $request->input('currency'),
                    'price' => $request->input('price'),
                    'total_price' => $request->input('price'),
                ]
            );

        $tariff = MeterTariff::with(
            [
                'accessRate',
                'pricingComponent',
                'socialTariff',
                'tou'
            ]
        )->find($newTariff->id);

        return response($tariff)->setStatusCode(201);
    }

    /**
     * Update of the specified tariff.
     * @urlParam tariffId required.
     * @param MeterTariff $tariff
     * @param TariffCreateRequest $request
     * @return ApiResource
     */
    public function update(MeterTariff $tariff, TariffCreateRequest $request): ApiResource
    {
        $result = $this->meterTariffService->update($tariff, $request);
        return new ApiResource($result);
    }

    public function usages(MeterTariff $tariff): ApiResource
    {
        $count = $this->meterTariffService->meterTariffUsageCount($tariff->id);
        return new ApiResource($count);
    }

    /**
     * Change Meter Tariff
     * @urlParam tariffId required
     * @urlParam changeID required
     * @param MeterTariff $tariff
     * @param int $changeId
     * @return ApiResource
     */
    public function changeMetersTariff(MeterTariff $tariff, int $changeId): ApiResource
    {
        $currentId = $tariff->id;

        $result = $this->meterTariffService->changeMetersTariff($currentId, $changeId);
        return new ApiResource($result);
    }

    /**
     * Remove of the specified tariff.
     * @urlParam tariffId required.
     * @param MeterTariff $tariff
     * @return bool|null
     */
    public function destroy(MeterTariff $tariff): ?bool
    {
        return $tariff->delete();
    }
}
