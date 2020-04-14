<?php

namespace App\Helpers;

class PowerConverter
{
    private static $powerUnits = [
        "W" => 1,
        "kW" => 1000,
        "MW" => 1000000,
        'Wh' => 1,
        'kWh' => 1000,
        'MWh' => 1000000,
    ];

    public static function convert($power, $powerUnit, $expectedUnit = 'Wh')
    {

        return $power * self::$powerUnits[$powerUnit] / self::$powerUnits[$expectedUnit];
    }
}
