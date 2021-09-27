<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\SmsResendInformationKey;
use App\Services\SmsResendInformationKeyService;
use Illuminate\Http\Request;

/**
 * @group   Sms Resend Key
 * Class SmsResendInformationKeyController
 * @package App\Http\Controllers
 */
class SmsResendInformationKeyController extends Controller
{
    private $smsResendInformationKeyService;


    public function __construct(SmsResendInformationKeyService $smsResendInformationKeyService)
    {

        $this->smsResendInformationKeyService = $smsResendInformationKeyService;
    }

    /**
     * List of all resend information keys.
     * A list of the all resend information keys.
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->smsResendInformationKeyService->getResendInformationKeys());
    }

    /**
     * Update Resend information key.
     * Update of the specified Resend information key.
     * @urlParam smsResendInformationKeyId required
     * @bodyParam key string required
     * @param SmsResendInformationKey $smsResendInformationKey
     * @param Request $request
     * @return ApiResource
     */
    public function update(SmsResendInformationKey $smsResendInformationKey, Request $request): ApiResource
    {
        return new ApiResource($this->smsResendInformationKeyService->updateResendInformationKey(
            $smsResendInformationKey,
            $request->all()
        ));
    }
}
