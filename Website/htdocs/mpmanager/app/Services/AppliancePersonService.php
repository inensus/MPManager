<?php

namespace App\Services;

class AppliancePersonService
{

    public function initSoldApplianceDataContainer($assetType, $assetPerson, $transaction)
    {
        $soldApplianceDataContainer = app()->makeWith(
            'App\Misc\SoldApplianceDataContainer',
            [
                'assetType' => $assetType,
                'assetPerson' => $assetPerson,
                'transaction' => $transaction
            ]
        );
        event('appliance.sold', $soldApplianceDataContainer);
    }
}
