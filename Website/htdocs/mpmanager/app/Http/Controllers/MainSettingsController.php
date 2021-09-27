<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MainSettings;

/**
 * @group   Main Settings
 * Class MainSettingsController
 * @package App\Http\Controllers
 */

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

    /**
     * List main settings.
     * A list of the all main settings.
     * @responseFile responses/settings/main.settings.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(MainSettings::all());
    }

    /**
     * Update main settings.
     * @bodyParam site_title string
     * @bodyParam company_name string
     * @bodyParam currency string
     * @bodyParam country string
     * @bodyParam language string
     * @bodyParam vat_energy float
     * @bodyParam vat_appliance float
     * @param MainSettings $mainSettings
     * @return ApiResource
     */
    public function update(MainSettings $mainSettings): ApiResource
    {
        $mainSettings = MainSettings::updateOrCreate(
            [
                'id' => request('id')
            ],
            [   'site_title' => request('site_title'),
                'company_name' => request('company_name'),
                'currency' => request('currency'),
                'country' => request('country'),
                'language' => request('language'),
                'vat_energy' => request('vat_energy'),
                'vat_appliance' => request('vat_appliance')
            ]
        );
        return new ApiResource([$mainSettings->fresh()]);
    }
}
