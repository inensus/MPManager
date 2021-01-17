<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\TicketSettings;


class TicketSettingsController extends Controller
{
    /**
     *  @TicketSettings
     */

    private $ticketSettings;

    public function __construct(TicketSettings $ticketSettings)
    {
        $this->ticketSettings = $ticketSettings;

    }

    public function index(): ApiResource
    {
        $ticketSettings = TicketSettings::all();
        return new ApiResource($ticketSettings);
    }

    public function update(TicketSettings $ticketSettings): ApiResource
    {
        $ticketSettings = $this->ticketSettings->find(request('id'));
        $ticketSettings->name = request('name');
        $ticketSettings->api_token = request('api_token');
        $ticketSettings->api_url = request('api_url');
        $ticketSettings->api_key = request('api_key');

        $ticketSettings->save();
        $ticketSettings->update([
            'updated_at' => date('Y-m-d h:i:s')]);
        return new ApiResource($ticketSettings);
    }
}
