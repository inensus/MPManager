<?php

namespace App\Sms\Senders;

use App\Models\Transaction\Transaction;
use App\Services\SmsBodyService;

class TransactionConfirmation extends SmsSender
{
    protected $data;
    public $body = '';
    protected $references = [
        'header' => 'SmsTransactionHeader',
        'body' => null,
        'footer' => 'SmsTransactionFooter'
    ];
    public const ENERGY_CONFIRMATION = 'energy';
    public const ACCESS_RATE_PAYMENT = 'access rate';
    public const ASSET_RATE_PAYMENT = 'loan rate';

    public function prepareBody()
    {
        $this->data->paymentHistories()->each(function ($payment) {
            switch ($payment->payment_type) {
                case self::ENERGY_CONFIRMATION:
                    $this->prepareBodyByClassReference('EnergyConfirmation', $payment);
                    break;
                case self::ACCESS_RATE_PAYMENT:
                    $this->prepareBodyByClassReference('AccessRateConfirmation', $payment);
                    break;
                case self::ASSET_RATE_PAYMENT:
                    $this->prepareBodyByClassReference('AssetRatePayment', $payment);
                    break;
            }
        });

        $this->preparePricingDetails();
    }
    public function preparePricingDetails()
    {
        $this->prepareBodyByClassReference('PricingDetails', $this->data);
    }
    private function prepareBodyByClassReference($reference, $payload)
    {
        $smsBody = $this->smsBodyService->getSmsBodyByReference($reference);
        $className = 'App\\Sms\\BodyParsers\\' . $reference;
        $smsObject = new $className($payload);

        $this->body .= $smsObject->parseSms($smsBody->body);
    }
}
