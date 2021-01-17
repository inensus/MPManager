<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MapSettings;

class MapSettingsController extends Controller
{
    /**
     *  @MapSettings
     */

    private $mapSettings;

    public function __construct(MapSettings $mapSettings)
    {
        $this->mapSettings = $mapSettings;

    }

    public function index(): ApiResource
    {
        $mapSettings = MapSettings::all();
        return new ApiResource($mapSettings);
    }

    public function update(MapSettings $mapSettings): ApiResource
    {
        $mapSettings = $this->mapSettings->find(request('id'));
        $mapSettings->zoom = request('zoom');
        $mapSettings->latitude = request('latitude');
        $mapSettings->longitude = request('longitude');
        $mapSettings->save();
        $mapSettings->update([
            'updated_at' => date('Y-m-d h:i:s')]);
        return new ApiResource($mapSettings);
    }
}
