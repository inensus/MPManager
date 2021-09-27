<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmsAndroidSettingRequest;
use App\Http\Resources\ApiResource;
use App\Models\SmsAndroidSetting;
use App\Services\SmsAndroidSettingService;

/**
 * @group   Sms Android Settings
 * Class SmsAndroidSettingController
 * @package App\Http\Controllers
 */
class SmsAndroidSettingController extends Controller
{

    private $smsAndroidSettingService;

    public function __construct(SmsAndroidSettingService $smsAndroidSettingService)
    {
        $this->smsAndroidSettingService = $smsAndroidSettingService;
    }

    /**
     * List of Sms Android Settings
     * @responseFile responses/sms/sms.android.settings.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->smsAndroidSettingService->getSmsAndroidSetting());
    }

    /**
     * Create a new sms android settings.
     * @bodyParam token string required
     * @bodyParam key string required
     * @bodyParam call_back string required
     * @param SmsAndroidSettingRequest $request
     * @return ApiResource
     */
    public function store(SmsAndroidSettingRequest $request): ApiResource
    {
        return new ApiResource($this->smsAndroidSettingService->createSmsAndroidSetting($request->all()));
    }

    /**
     * Update sms android settings.
     * @urlParam smsAndroidSettingId required
     * @bodyParam token string
     * @bodyParam key string
     * @bodyParam callback string
     * @param SmsAndroidSetting $smsAndroidSetting
     * @param SmsAndroidSettingRequest $request
     * @return ApiResource
     */
    public function update(SmsAndroidSetting $smsAndroidSetting, SmsAndroidSettingRequest $request)
    {
        return new ApiResource($this->smsAndroidSettingService->updateSmsAndroidSetting(
            $smsAndroidSetting,
            $request->only(['token', 'key', 'callback'])
        ));
    }

    /**
     * Remove of the specified sms android setting.
     * @urlParam smsAndroidSettingId required
     * @param SmsAndroidSetting $smsAndroidSetting
     * @return ApiResource
     */
    public function destroy(SmsAndroidSetting $smsAndroidSetting): ApiResource
    {
        return new ApiResource($this->smsAndroidSettingService->deleteSmsAndroidSetting($smsAndroidSetting));
    }
}
