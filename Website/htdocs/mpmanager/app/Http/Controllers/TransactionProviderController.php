<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Services\TransactionProviderService;

/**
 * @group   Transactions
 * Class TransactionProviderController
 * @package App\Http\Controllers
 */
class TransactionProviderController extends Controller
{
     private $transactionProviderService;

    public function __construct(TransactionProviderService $transactionProviderService)
    {
        $this->transactionProviderService = $transactionProviderService;
    }

    /**
     * List
     * A list of the all transaction providers.
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        return new ApiResource($this->transactionProviderService->getTransactionProviders());
    }
}
