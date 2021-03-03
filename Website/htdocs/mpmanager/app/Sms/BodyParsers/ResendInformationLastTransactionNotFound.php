<?php


namespace App\Sms\BodyParsers;

class ResendInformationLastTransactionNotFound extends SmsBodyParser
{
    protected $variables = ['meter'];

    protected $meterSerial;
    public function __construct($meterSerial)
    {
        $this->meterSerial=$meterSerial;
    }
    protected function getVariableValue($variable) {

        return $this->meterSerial;
    }
}