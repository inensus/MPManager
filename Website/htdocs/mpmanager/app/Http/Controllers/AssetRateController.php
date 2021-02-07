<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetRate;
use Illuminate\Http\Request;

class AssetRateController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AssetRate $assetRate
     * @return ApiResource
     */
    public function update(Request $request, AssetRate $assetRate): ApiResource
    {
        // notify log listener
        event(
            'new.log',
            [
                'logData' => [
                    'user_id' => $request->get('admin_id'),
                    'affected' => $assetRate->assetPerson,
                    'action' => 'Remaining rate ' . $assetRate->due_date . ' cost updated. From '
                        . $assetRate->remaining . ' to ' . $request->get('remaining')
                ]
            ]
        );
        $assetRate->remaining = $request->get('remaining');
        $assetRate->update();
        return new ApiResource($assetRate);
    }
}
