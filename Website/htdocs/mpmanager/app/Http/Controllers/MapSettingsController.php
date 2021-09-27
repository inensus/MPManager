<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidBingApiKeyException;
use App\Http\Resources\ApiResource;
use App\Http\Services\BingMapApiService;
use App\Models\MapSettings;

/**
 * @group   Map Settings
 * Class MapSettingsController
 * @package App\Http\Controllers
 */
class MapSettingsController extends Controller
{

    private $bingMapApiService;

    public function __construct(BingMapApiService $bingMapApiService)
    {
        $this->bingMapApiService = $bingMapApiService;
    }

    /**
     * List Map Settings
     * A list of the all map settings.
     * @responseFile responses/settings/map.settings.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(MapSettings::all());
    }

    /**
     * Update Map Settings.
     * Update map settings.
     * @bodyParam zoom int
     * @bodyParam latitude double
     * @bodyParam longitude double
     * @bodyParam provider string
     * @bodyParam bingMapApiKey string
     * @param $id
     * @return ApiResource
     */
    public function update($id): ApiResource
    {
        $mapSettings = MapSettings::query()
            ->updateOrCreate(
                ['id' => $id],
                [
                    'zoom' => request('zoom'),
                    'latitude' => request('latitude'),
                    'longitude' => request('longitude'),
                    'provider' => request('provider'),
                    'bingMapApiKey' => request('bingMapApiKey')
                ]
            );
        return new ApiResource([$mapSettings->fresh()]);
    }

    /**
     * Bing Api Key
     * Checks bing map api key on bing map website.
     * @bodyParam key string
     * @param $key
     * @return ApiResource
     */
    public function checkBingApiKey($key): ApiResource
    {
        try {
            $apiAuthentication = $this->bingMapApiService->checkAuthenticationKey($key);
        } catch (InvalidBingApiKeyException $exception) {
            $apiAuthentication = false;
        }
        return ApiResource::make(['authentication' => $apiAuthentication]);
    }
}
