<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MapSettings;

class MapSettingsController extends Controller
{
    /**
     * @MapSettings
     */

    private $mapSettings;

    public function __construct(MapSettings $mapSettings)
    {
        $this->mapSettings = $mapSettings;
    }

    public function index(): ApiResource
    {
        return new ApiResource(MapSettings::all());
    }

    public function update(MapSettings $mapSettings): ApiResource
    {
        $mapSettings->update(
            request()->only(
                [
                'zoom', 'latitude', 'longitude'
                ]
            )
        );
        return new ApiResource($mapSettings->fresh());
    }
}
