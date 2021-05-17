<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MapSettings;
use App\Http\Services\BingMapApiService;

class MapSettingsController extends Controller
{
    /**
     * @MapSettings
     */

    private $mapSettings;

    public function __construct(MapSettings $mapSettings, BingMapApiService $bingMapApiService)
    {
        $this->mapSettings = $mapSettings;
        $this->bingMapApiService = $bingMapApiService;
    }

    public function index(): ApiResource
    {
        return new ApiResource(MapSettings::all());
    }

    public function update(MapSettings $mapSettings): ApiResource
    {
        $mapSettings = MapSettings::updateOrCreate(
            [
              'id' => request('id')
            ],
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

    public function checkBingApiKey(): ApiResource
    {
        $key = \request('key');
        return new ApiResource($this->bingMapApiService->checkBingApiKey($key));

    }

}
