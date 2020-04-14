<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 07.06.18
 * Time: 11:28
 */

namespace App\Lib;

use App\Models\Meter\MeterToken;

interface IManufacturerAPI
{
    /**
     * @param $tokenData
     * @return MeterToken
     */
    public function generateToken($tokenData): MeterToken;

}
