<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 28.06.18
 * Time: 11:47
 */

namespace App\Sms;

class SmsTypes
{
    public const TRANSACTION_CONFIRMATION = 1;
    public const APPLIANCE_RATE = 2;
    public const OVER_DUE_APPLIANCE_RATE = 3;
    public const MANUAL_SMS = 4;
    public const RESEND_INFORMATION = 5;
}
