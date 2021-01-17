<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\MainSettings;

class MainSettingsController extends Controller
{
    /**
     *  @MainSettings
     */

    private $mainSettings;

    public function __construct(MainSettings $mainSettings)
    {
        $this->mainSettings = $mainSettings;

    }

    public function index(): ApiResource
    {
        $mainSettings = MainSettings::all();
        return new ApiResource($mainSettings);
    }

    public function update(MainSettings $mainSettings): ApiResource
    {
        $mainSettings = $this->mainSettings->find(request('id'));
        $mainSettings->site_title = request('site_title');
        $mainSettings->company_name = request('company_name');
        $mainSettings->currency = request('currency');
        $mainSettings->country = request('country');
        $mainSettings->language = request('language');
        $mainSettings->vat_energy = request('vat_energy');
        $mainSettings->vat_appliance = request('vat_appliance');
        $mainSettings->update();
        return new ApiResource($mainSettings);
    }
}
