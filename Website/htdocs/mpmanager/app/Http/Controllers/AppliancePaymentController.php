<?php

namespace App\Http\Controllers;

use App\Models\AssetPerson;
use App\Services\AppliancePaymentService;
use Illuminate\Http\Request;

/**
 * @group   Appliance Payment
 * Class AppliancePaymentController
 * @package App\Http\Controllers
 */

class AppliancePaymentController extends Controller
{

    private $appliancePaymentService;

    public function __construct(AppliancePaymentService $appliancePaymentService)
    {
        $this->appliancePaymentService = $appliancePaymentService;
    }

    /**
     * Get payment for the sold appliance by given person.
     *
     * @urlParam appliancePersonId required
     *
     * @bodyParam amount float required.
     * @param AssetPerson $appliancePerson
     * @param Request $request
     * @throws \App\Exceptions\PaymentAmountBiggerThanTotalRemainingAmount
     * @throws \App\Exceptions\PaymentAmountSmallerThanZero
     */
    public function store(AssetPerson $appliancePerson, Request $request)
    {
        $this->appliancePaymentService->getPaymentForAppliance($request, $appliancePerson);
    }
}
