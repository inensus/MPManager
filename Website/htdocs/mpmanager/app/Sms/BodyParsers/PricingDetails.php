<?php


namespace App\Sms\BodyParsers;


use App\Models\MainSettings;
use App\Models\Transaction\Transaction;

class PricingDetails   extends SmsBodyParser
{
    public $variables = ['amount','vat_energy','vat_others'];
    protected $transaction;
    private  $vatEnergy = 0;
    private  $vatOtherStaffs = 0;
    public function __construct(Transaction $transaction)
    {
        $this->transaction=$transaction;
        $this->calculateTaxes();
    }

    protected function getVariableValue($variable) {
        switch($variable) {
            case 'amount':
                $variable = $this->transaction->amount;
                break;
            case 'vat_energy':

                $variable = $this->vatEnergy;
                break;
            case 'vat_others':
                $variable = $this->vatOtherStaffs;
                break;
        }
        return $variable;
    }

    private function calculateTaxes()
    {
        $mainSettings= MainSettings::query()->first();
        $totalEnergyAmount=0;
        $otherStaffsAmount=0;
        $this->transaction->paymentHistories()->get()->each(function($payment) use ($totalEnergyAmount,$otherStaffsAmount) {
            if (!$payment->payment_type==='energy'){
                $otherStaffsAmount +=  $payment->amount;
                return true;
            }
            $totalEnergyAmount += $payment->amount;
            return  true;
        });
        $this->vatEnergy=$totalEnergyAmount*$mainSettings->vat_energy / 100;
        $this->vatOtherStaffs=$otherStaffsAmount*$mainSettings->vat_appliance / 100;
    }

}