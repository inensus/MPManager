<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ApiResource;
use App\Services\SmsBodyService;

/**
 * @group   Sms Body
 * Class SmsBodyController
 * @package App\Http\Controllers
 */
class SmsBodyController extends Controller
{
    private $smsBodyService;

    public function __construct(SmsBodyService $smsBodyService)
    {
        $this->smsBodyService = $smsBodyService;
    }

    /**
     * List of all Sms Bodies
     * A list of the all sms bodies.
     * @responseFile responses/sms/sms.bodies.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->smsBodyService->getSmsBodies());
    }

    /**
     * Updates of the specified sms body.
     * @urlParam body string
     * @param Request $request
     * @return ApiResource
     */
    public function update(Request $request): ApiResource
    {
        return new ApiResource($this->smsBodyService->updateSmsBodies($request->all()));
    }
}
