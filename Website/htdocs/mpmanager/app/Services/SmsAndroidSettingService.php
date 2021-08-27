<?php

namespace App\Services;

use App\Models\Sms;
use App\Models\SmsAndroidSetting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

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
            'callback' => $smsAndroidSettingData['callback'],
            'token' => $smsAndroidSettingData['token'],
            'key' => $smsAndroidSettingData['key']
        ]);
        return $this->smsAndroidSetting->newQuery()->get();
    }

    public function deleteSmsAndroidSetting(SmsAndroidSetting $smsAndroidSetting)
    {
        $smsAndroidSetting->delete();
        return $this->smsAndroidSetting->newQuery()->get();
    }

    public function getResponsible()
    {
        $smsAndroidSettings = SmsAndroidSetting::all();
        if ($smsAndroidSettings->count() !== 0) {
            try {
                $lastSms = Sms::query()->latest()->select('id')->take(1)->firstOrFail()->id;
                $responsibleGateway = $smsAndroidSettings[$lastSms % $smsAndroidSettings->count()];
            } catch (ModelNotFoundException $e) {
                $responsibleGateway = $smsAndroidSettings[0];
            }
            return $responsibleGateway;
        } else {
            Log::critical(
                'Sms Android Settings is empty'
            );
            return ;
        }
    }
}
