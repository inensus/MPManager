<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 07.06.18
 * Time: 11:28
 */

namespace App\Lib;

use App\Models\Meter\Meter;
use App\Models\Meter\MeterToken;

interface IManufacturerAPI
{
    /**
     * @param Meter $meter
     * @param $energy
     * @return array
     */
    public function  chargeMeter(Meter $meter, $energy): array;

    public function clearMeter(Meter $meter);
}
