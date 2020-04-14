<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 08.06.18
 * Time: 16:39
 */

namespace App\Lib;

interface ISmsProvider
{
    /**
     * Sends the sms to the sms provider
     * @param string $number
     * @param string $body
     * @param string $callback
     * @return mixed
     */
    public function sendSms(string $number, string $body, string $callback);
}
