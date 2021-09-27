<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmsApplianceRemindRateRequest;
use App\Http\Resources\ApiResource;
use App\Models\SmsApplianceRemindRate;
use App\Services\SmsApplianceRemindRateService;
use Illuminate\Http\Request;

/**
 * @group   Sms Appliance Reminder
 * Class SmsApplianceRemindRateController
 * @package App\Http\Controllers
 */

class SmsApplianceRemindRateController extends Controller
{

    private $smsApplianceRemindService;

    public function __construct(SmsApplianceRemindRateService $smsApplianceRemindService)
    {
        $this->smsApplianceRemindService = $smsApplianceRemindService;
    }

    /**
     * List of all Appliance Reminder Smses
     * A list of the all appliance reminder smses.
     * @responseFile responses/sms/sms.appliance.reminder.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->smsApplianceRemindService->getApplianceRemindRatesWithApplianceTypes());
    }

    /**
     * Create a new appliance reminder sms.
     * @bodyParam appliance_type_id int
     * @bodyParam overdue_remind_rate int
     * @bodyParam remind_rate int
     * @param SmsApplianceRemindRateRequest $request
     * @return ApiResource
     */
    public function store(SmsApplianceRemindRateRequest $request): ApiResource
    {
        return new ApiResource($this->smsApplianceRemindService->createApplianceRemindRate($request->all()));
    }

    /**
     * Update of the specified appliance reminder sms.
     * @urlParam SmsApplianceRemindRateId required
     * @bodyParam appliance_type_id int
     * @bodyParam overdue_remind_rate int
     * @bodyParam remind_rate int
     * @param SmsApplianceRemindRate $smsApplianceRemindRate
     * @param Request $request
     * @return ApiResource
     */
    public function update(SmsApplianceRemindRate $smsApplianceRemindRate, Request $request): ApiResource
    {
        return new ApiResource($this->smsApplianceRemindService->updateApplianceRemindRate(
            $smsApplianceRemindRate,
            $request->all()
        ));
    }
}
