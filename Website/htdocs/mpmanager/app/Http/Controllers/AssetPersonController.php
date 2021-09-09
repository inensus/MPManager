<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\AssetPerson;
use App\Models\AssetType;
use App\Models\Person\Person;
use App\Services\AppliancePersonService;
use App\Services\CashTransactionService;
use Illuminate\Http\Request;

class AssetPersonController extends Controller
{

    /**
     * @var AssetPerson
     */
    private $assetPerson;

    private $assetPersonService;

    private $cashTransactionService;

    public function __construct(
        AssetPerson $assetPerson,
        AppliancePersonService $assetPersonService,
        CashTransactionService $cashTransactionService
    ) {
        $this->assetPerson = $assetPerson;
        $this->assetPersonService = $assetPersonService;
        $this->cashTransactionService = $cashTransactionService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AssetType $assetType
     * @param  Person    $person
     * @param  Request   $request
     * @return ApiResource
     */
    public function store(
        AssetType $assetType,
        Person $person,
        Request $request
    ): ApiResource {
        $assetPerson = $this->assetPerson::query()->make(
            [
            'person_id' => $person->id,
            'asset_type_id' => $assetType->id,
            'total_cost' => $request->get('cost'),
            'down_payment' => $request->get('downPayment'),
            'rate_count' => $request->get('rate'),
            'creator_id' => $request->get('creatorId')

            ]
        );
        $buyerAddress = $person->addresses()->where('is_primary', 1)->first();
        $sender = $buyerAddress == null ? '-' : $buyerAddress->phone;
        $transaction = null;
        if ((int)$request->get('downPayment') > 0) {
            $transaction = $this->cashTransactionService->createCashTransaction(
                $request->get('creatorId'),
                $request->get('downPayment'),
                $sender
            );
        }

        $assetPerson->save();

        $this->assetPersonService->initSoldApplianceDataContainer(
            $assetType,
            $assetPerson,
            $transaction
        );

        return new ApiResource($assetPerson);
    }

    /**
     * Display the specified resource.
     *
     * @param  Person  $person
     * @param  Request $request
     * @return ApiResource
     */
    public function index(Person $person, Request $request): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('person_id', $person->id)
            ->get();
        return new ApiResource($assets);
    }

    public function show($applianceId): ApiResource
    {
        $assets = $this->assetPerson::with('assetType', 'rates.logs', 'logs.owner')
            ->where('id', '=', $applianceId)
            ->get();
        return new ApiResource($assets);
    }
}
