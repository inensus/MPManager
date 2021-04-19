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
        $mapSettings = MapSettings::updateOrCreate(
            [
              'id'=>request('id')
            ],
            [
                'zoom'=>request('zoom'),
                'latitude'=>request('latitude'),
                'longitude'=>request('longitude')
            ]
        );
        return new ApiResource([$mapSettings->fresh()]);
    }
}
