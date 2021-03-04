<?php


namespace App\Services;


use App\Models\SmsAndroidSetting;

class SmsAndroidSettingService
{

    private $smsAndroidSetting;

    public function __construct(SmsAndroidSetting $smsAndroidSetting)
    {
        $this->smsAndroidSetting = $smsAndroidSetting;
    }


    public function getSmsAndroidSetting()
    {
        return $this->smsAndroidSetting->newQuery()->get();
    }

    public function createSmsAndroidSetting($smsAndroidSettingData)
    {
        $this->smsAndroidSetting->newQuery()->create($smsAndroidSettingData);
        return $this->smsAndroidSetting->newQuery()->get();
    }

    public function updateSmsAndroidSetting(SmsAndroidSetting $smsAndroidSetting, $smsAndroidSettingData)
    {

        $smsAndroidSetting->update([
            'callback'=>$smsAndroidSettingData['callback'],
            'token'=>$smsAndroidSettingData['token'],
            'key'=>$smsAndroidSettingData['key']
        ]);
        return $this->smsAndroidSetting->newQuery()->get();
    }

    public function deleteSmsAndroidSetting(SmsAndroidSetting $smsAndroidSetting)
    {
        $smsAndroidSetting->delete();
        return $this->smsAndroidSetting->newQuery()->get();
    }
}