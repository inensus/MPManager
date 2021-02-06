<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MainSettings;

class MainSettingsController extends Controller
{
    /**
     * @MainSettings
     */

    private $mainSettings;

    public function __construct(MainSettings $mainSettings)
    {
        $this->mainSettings = $mainSettings;
    }

    public function index(): ApiResource
    {
        return new ApiResource(MainSettings::all());
    }

    public function update(MainSettings $mainSettings): ApiResource
    {
        $mainSettings->update(
            request()->only(
                [
                'site_title', 'company_name', 'currency', 'country', 'language', 'vat_energy', 'vat_appliance'
                ]
            )
        );
        return new ApiResource($mainSettings->fresh());
    }
}
