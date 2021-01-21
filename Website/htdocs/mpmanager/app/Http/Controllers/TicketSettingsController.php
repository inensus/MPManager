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
        return new ApiResource(TicketSettings::all());
    }

    public function update(TicketSettings $ticketSettings): ApiResource
    {
        $ticketSettings->update(request()->only([
            'name', 'api_token', 'api_url', 'api_key'
        ]));
        return new ApiResource($ticketSettings->fresh());
    }
}
