<?php


namespace App\Sms\Senders;


use App\Sms\BodyParsers\ResendInformation;
use App\Sms\BodyParsers\ResendInformationLastTransactionNotFound;
use Illuminate\Support\Collection;

class ResendInformationNotification extends SmsSender
{

    protected $data;
    protected $body = '';
    protected $references = [
        'header' => 'SmsResendInformationHeader',
        'body' => null,
        'footer' => 'SmsResendInformationFooter'
    ];

    public function prepareBody()
    {
        if (!is_array($this->data)) {
            $smsBody = $this->smsBodyService->getSmsBodyByReference('ResendInformation');
            $this->data->paymentHistories()->each(function ($payment) use ($smsBody) {
                $smsObject = new ResendInformation($payment);
                $this->body .= $smsObject->parseSms($smsBody->body);
            });
        } else {
            $smsBody = $this->smsBodyService->getSmsBodyByReference('ResendInformationLastTransactionNotFound');
            $smsObject = new ResendInformationLastTransactionNotFound($this->data);
            $this->body .= $smsObject->parseSms($smsBody->body);
        }


    }
}