<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\TicketSettings;

/**
 * @group   Ticket
 * Class TicketSettingsController
 * @package App\Http\Controllers
 */
class TicketSettingsController extends Controller
{
    /**
     * @TicketSettings
     */

    private $ticketSettings;

    public function __construct(TicketSettings $ticketSettings)
    {
        $this->ticketSettings = $ticketSettings;
    }

    /**
     * List ticket settings.
     * A list of the all ticket settings.
     * @responseFile responses/settings/ticket.settings.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource(TicketSettings::all());
    }

    /**
     * Update ticket settings.
     * Update of the ticket settings.
     * @bodyParam name string
     * @bodyParam api_token string
     * @bodyParam api_url string
     * @bodyParam api_key string
     * @param TicketSettings $ticketSettings
     * @return ApiResource
     */
    public function update(TicketSettings $ticketSettings): ApiResource
    {
        $ticketSettings = TicketSettings::updateOrCreate(
            [
              'id' => request('id')
            ],
            [
                'name' => request('name'),
                'api_token' => request('api_token'),
                'api_url' => request('api_url'),
                'api_key' => request('api_key')
            ]
        );

        return new ApiResource($ticketSettings->fresh());
    }
}
