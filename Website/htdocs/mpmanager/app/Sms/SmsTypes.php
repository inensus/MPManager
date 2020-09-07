<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 28.06.18
 * Time: 11:47
 */

namespace App\Sms;


use App\Misc\TransactionDataContainer;
use App\Models\AssetRate;
use App\Models\PaymentHistory;
use App\Models\Transaction\Transaction;

class SmsTypes
{
    public const ENERGY_CONFIRMATION = 'energy';
    public const ACCESS_RATE_PAYMENT = 'access rate';
    public const ASSET_RATE_PAYMENT = 'loan rate';
    public const RESEND_INFORMATION = 2;
    public const ASSET_RATE_REMINDER = 3;
    public const ASSET_RATE_OVER_DUE_REMINDER = 4;


    public static function generateSmsBody($data)
    {


        if ($data instanceof Transaction) {
            $payments = $data->paymentHistories()->get();
            $body = '';
            foreach ($payments as $payment) {
                $body .= self::paymentTypeBody($payment);
            }
            $body .= self::pricingDetails($data);
        } elseif ($data instanceof AssetRate) {
            return self::generateAssetRateReminder($data);
        }

        return $body;
    }

    private static function paymentTypeBody($payment)
    {
        switch ($payment->payment_type) {
            case self::ENERGY_CONFIRMATION :
                return self::generateEnergyConfirmationBody($payment);
                break;
            case self::ACCESS_RATE_PAYMENT:
                return self::generateAccessRateConfirmationBody($payment);
                break;
            case self::RESEND_INFORMATION:
                return self::generateResendInformation($payment);
                break;
            case self::ASSET_RATE_REMINDER:
                return self::generateAssetRateReminder($payment);
                break;
            case self::ASSET_RATE_PAYMENT:
                return self::generateAssetRatePayment($payment);
                break;
            case self::ASSET_RATE_OVER_DUE_REMINDER:
                return self::generateOverDueAssetRateReminder($payment);
                break;

        }
    }

    private static function pricingDetails(Transaction $transaction)
    {
        return " VAT 18% \t" . round($transaction->amount * 0.18,
                0) . " \n Jumla \t " . round($transaction->amount * 0.82, 0);
    }

    private static function generateAccessRateConfirmationBody(PaymentHistory $paymentHistory)
    {
        return 'Service Charge: TZS ' . $paymentHistory->amount . ' ';
    }


    private static function generateAssetRatePayment(PaymentHistory $paymentHistory)
    {
        $asset = $paymentHistory->paidFor()->first()->asset()->first();

        return 'Appliance: ' . $asset->name . ' Tshs ' . $paymentHistory->amount . '. ';
    }

    private static function generateEnergyConfirmationBody(PaymentHistory $paymentHistory): string
    {

        $token = $paymentHistory->paidFor()->first();
        $transaction = $paymentHistory->transaction()->first();

        return 'LUKU: {' . $transaction->message . '}, ' . $token->token . ' Unit ' . $token->energy . '. TZS ' . $paymentHistory->amount;

    }

    private static function generateResendInformation(TransactionDataContainer $transactionContainer): string
    {

        $smsContent = 'LUKU: {' . $transactionContainer->meter->serial_number . '}, ' . $transactionContainer->token->token . ' Unit ' . $transactionContainer->token->energy . ' KWH.  Service Charge: Tshs ' . ($transactionContainer->historyAccessRate->amount ?? '0.00') . ' \n';
        if (!empty($transactionContainer->paid_rates)) {
            foreach ($transactionContainer->paid_rates as $paid_rate) {
                $smsContent .= $paid_rate['asset_type_name'] . ' ' . $paid_rate['paid'] . ' TZS \n';
            }
        }
        $smsContent .= 'VAT 18% \t ' . round((float)($transactionContainer->historyEnergy->amount ?? 0) * 0.18) . ' \n
                        Jumla \t ' . round((float)($transactionContainer->historyEnergy->amount ?? 0) * 0.82);

        return $smsContent;
    }

    private static function generateAssetRateReminder($reminderData): string
    {
        $smsContent = 'Dear ' . $reminderData->assetPerson->person->name . ' ' . $reminderData->assetPerson->person->surname . ', the next rate of ' . $reminderData->assetPerson->assetType->name . '(' . $reminderData->remaining . ') is due on  ' . $reminderData->due_date . ' \n' .
            'Your Company';

        return $smsContent;
    }

    private static function generateOverDueAssetRateReminder($reminderData): string
    {
        $smsContent = 'Dear ' . $reminderData->assetPerson->person->name . ' ' . $reminderData->assetPerson->person->surname . ', you forgot to pay the rate of ' . $reminderData->assetPerson->assetType->name . '(' . $reminderData->remaining . ')  on  ' . $reminderData->due_date . ' \n' .
            'Please pay it as soon as possible, unless you wont be able to buy energy \n' .
            'Your Company';

        return $smsContent;
    }
}
