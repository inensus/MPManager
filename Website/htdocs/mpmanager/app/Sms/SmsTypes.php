<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 28.06.18
 * Time: 11:47
 */

namespace App\Sms;


use App\Misc\TransactionDataContainer;
use App\Models\Transaction\Transaction;

class SmsTypes
{
    public const ENERGY_CONFIRMATION = 0;
    public const ACCESS_RATE_PAYMENT = 1;
    public const RESEND_INFORMATION = 2;
    public const ASSET_RATE_REMINDER = 3;
    public const ASSET_RATE_OVER_DUE_REMINDER = 4;


    public static function generateSmsBody(int $type, $data)
    {
        switch ($type) {
            case self::ENERGY_CONFIRMATION :
                return self::generateEnergyConfirmationBody($data);
                break;
            case self::ACCESS_RATE_PAYMENT:
                return self::generateAccessRateConfirmationBody($data);
                break;
            case self::RESEND_INFORMATION:
                return self::generateResendInformation($data);
                break;
            case self::ASSET_RATE_REMINDER:
                return self::generateAssetRateReminder($data);
                break;
            case self::ASSET_RATE_OVER_DUE_REMINDER:
                return self::generateOverDueAssetRateReminder($data);
                break;

        }
    }

    private static function generateAccessRateConfirmationBody(TransactionDataContainer $transactionContainer)
    {
        $smsContent = "Gharama ya umeme: TZs 0 Service charge: TZs " . $transactionContainer->accessRateDebt . '\n';
        if (!empty($transactionContainer->paid_rates)) {
            foreach ($transactionContainer->paid_rates as $paid_rate) {
                $smsContent .= $paid_rate['asset_type_name'] . ' ' . $paid_rate['paid'] . ' TZS \n';
            }
        }
        $smsContent .= 'Your Company';
        return $smsContent;
    }

    private static function generateEnergyConfirmationBody(TransactionDataContainer $transactionContainer): string
    {
        $smsContent = 'LUKU: {' . $transactionContainer->meter->serial_number . '}, ' . $transactionContainer->token->token . ' Unit ' . $transactionContainer->token->energy . ' KWH.  Service Charge: Tshs ' . $transactionContainer->accessRateDebt ?? '0.00' . ' \n';
        if (!empty($transactionContainer->paid_rates)) {
            foreach ($transactionContainer->paid_rates as $paid_rate) {
                $smsContent .= $paid_rate['asset_type_name'] . ' ' . $paid_rate['paid'] . ' TZS \n';
            }
        }
        $smsContent .= ' VAT 18% \t' . round($transactionContainer->transaction->amount * 0.18, 0) . ' \n
                        Jumla \t ' . round($transactionContainer->transaction->amount * 0.82, 0);

        return $smsContent;
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
