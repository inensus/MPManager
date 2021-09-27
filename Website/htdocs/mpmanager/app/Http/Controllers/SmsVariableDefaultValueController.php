<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Services\SmsVariableDefaultValueService;

/**
 * @group   Sms Default Value
 * Class SmsVariableDefaultValueController
 * @package App\Http\Controllers
 */
class SmsVariableDefaultValueController extends Controller
{
    private $smsVariableDefaultSValueService;
    public function __construct(SmsVariableDefaultValueService $smsVariableDefaultSValueService)
    {
        $this->smsVariableDefaultSValueService = $smsVariableDefaultSValueService;
    }

    /**
     * List of All Default sms values.
     * A list of the all sms default values.
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->smsVariableDefaultSValueService->getSmsVariableDefaultValues());
    }
}
