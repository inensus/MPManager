<?php

/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 09.10.18
 * Time: 19:08
 */

namespace App\Http\Controllers;

use App\Http\Requests\SmsRequest;
use App\Http\Requests\StoreSmsRequest;
use App\Http\Resources\ApiResource;
use App\Http\Resources\SmsSearchResultResource;
use App\Models\ConnectionGroup;
use App\Models\ConnectionType;
use App\Models\Meter\MeterParameter;
use App\Models\Person\Person;
use App\Models\Sms;
use App\Services\SmsResendInformationKeyService;
use App\Services\SmsService;
use App\Sms\Senders\SmsConfigs;
use App\Sms\SmsTypes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inensus\Ticket\Services\CommentService;

/**
 * @group   Sms
 * Class SmsController
 * @package App\Http\Controllers
 */

class SmsController extends Controller
{
    /**
     * @var Sms
     */
    private $sms;
    /**
     * @var Person
     */
    private $person;
    /**
     * @var ConnectionGroup
     */
    private $connectionGroup;
    /**
     * @var ConnectionType
     */
    private $connectionType;
    /**
     * @var MeterParameter
     */
    private $meterParameter;


    private $smsService;

    private $commentService;

    /**
     * SmsController constructor.
     *
     * @param Sms $sms
     * @param Person $person
     * @param ConnectionGroup $connectionGroup
     * @param ConnectionType $connectionType
     * @param MeterParameter $meterParameter
     * @param SmsResendInformationKeyService $smsResendInformationKeyService
     * @param SmsService $smsService
     * @param CommentService $commentService
     */
    public function __construct(
        Sms $sms,
        Person $person,
        ConnectionGroup $connectionGroup,
        ConnectionType $connectionType,
        MeterParameter $meterParameter,
        SmsResendInformationKeyService $smsResendInformationKeyService,
        SmsService $smsService,
        CommentService $commentService
    ) {
        $this->sms = $sms;
        $this->person = $person;
        $this->connectionGroup = $connectionGroup;
        $this->connectionType = $connectionType;
        $this->meterParameter = $meterParameter;
        $this->smsService = $smsService;
        $this->commentService = $commentService;
    }

    /**
     * List of all Smses
     * @responseFile responses/sms/smses.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $list = $this->sms
            ::with('address.owner')
            ->orderBy('id', 'DESC')
            ->select('receiver', DB::raw('count(*) as total'))
            ->groupBy('receiver')
            ->paginate(20);
        return new ApiResource($list);
    }

    /**
     * Store Bulk Sms
     * @bodyParam type string required
     * @bodyParam receivers array required
     * @bodyParam message string required
     * @bodyParam miniGrid int required
     * @bodyParam senderId int required
     * @return void
     */
    public function storeBulk(Request $request)
    {
        $type = $request->get('type');
        $receivers = $request->get('receivers');
        $message = $request->get('message');
        $miniGrid = $request->get('miniGrid') ?? 0;
        $senderId = $request->get('senderId');
        if ($type === null) {
            return;
        }
        if ($type === 'person') {
            foreach ($receivers as $receiver) {
                $phone = $receiver;
                $smsData = [
                    'receiver' => $phone,
                    'body' => $message,
                    'direction' => 1,
                    'sender_id' => $senderId,
                ];
                $this->smsService->createSms($smsData);
                $data = [
                    'message' => $message,
                    'phone' => $phone
                ];
                $this->smsService->sendSms($data, SmsTypes::MANUAL_SMS, SmsConfigs::class);
            }
        } elseif ($type === 'group' || $type === 'type' || $type === 'all') {
            //get connection group meters and owners
            if ($type === 'group') {
                $meters = $this->meterParameter::with(
                    [
                        'owner.addresses' =>
                            static function ($q) {
                                $q->where('is_primary', 1);
                            },
                    ]
                )
                    ->whereHas(
                        'address',
                        static function ($q) use ($miniGrid) {
                            if ((int)$miniGrid === 0) {
                                $q->where('city_id', '>', 0);
                            } else {
                                $q->where('city_id', $miniGrid);
                            }
                        }
                    )->whereHas(
                        'connectionGroup',
                        function ($q) use ($receivers) {
                            $q->where('id', $receivers);
                        }
                    )->get();
            } elseif ($type === 'all') {
                $meters = $this->meterParameter::with(
                    [
                        'owner.addresses' =>
                            static function ($q) {
                                $q->where('is_primary', 1);
                            },
                    ]
                )
                    ->whereHas(
                        'address',
                        static function ($q) use ($miniGrid) {
                            if ((int)$miniGrid === 0) {
                                $q->where('city_id', '>', 0);
                            } else {
                                $q->where('city_id', $miniGrid);
                            }
                        }
                    )->get();
            } else {
                $meters = $this->meterParameter::with(
                    [
                        'owner.addresses' =>
                            static function ($q) {
                                $q->where('is_primary', 1);
                            },
                    ]
                )
                    ->whereHas(
                        'address',
                        static function ($q) use ($miniGrid) {
                            if ((int)$miniGrid === 0) {
                                $q->where('city_id', '>', 0);
                            } else {
                                $q->where('city_id', $miniGrid);
                            }
                        }
                    )->whereHas(
                        'connectionType',
                        function ($q) use ($receivers) {
                            $q->where('id', $receivers);
                        }
                    )->get();
            }


            $addresses = $meters->pluck('owner.addresses');
            foreach ($addresses as $address) {
                if ($address === null) {
                    continue;
                }
                $this->sms->newQuery()->create(
                    [
                        'receiver' => $address[0]->phone,
                        'body' => $message,
                        'direction' => 1,
                        'sender_id' => $senderId,
                    ]
                );
                $data = [
                    'message' => $message,
                    'phone' => $address[0]->phone
                ];
                $this->smsService->sendSms($data, SmsTypes::MANUAL_SMS, SmsConfigs::class);
            }
        }
    }

    /**
     * Store a single Sms
     * @bodyParam sender int required
     * @bodyParam message string required
     * @param StoreSmsRequest $request
     * @return ApiResource
     */
    public function store(StoreSmsRequest $request): ApiResource
    {
        $sender = $request->get('sender');
        $message = $request->get('message');
        $smsData = [
            'receiver' => $sender,
            'body' => $message,
            'direction' => 0,
            'sender_id' => null,
        ];
        $sms = $this->smsService->createSms($smsData);

        switch ($this->smsService->checkMessageType($message)) {
            case $this->smsService::FEEDBACK:
                event('sms.stored', [$sender, $message]);
                break;
            case $this->smsService::TICKET:
                $this->commentService->storeComment($sender, $message);
                break;
        }
        return new ApiResource($sms);
    }

    /**
     * Store and Send single Message
     * @bodyParam person_id int required
     * @bodyParam senderId int required
     * @bodyParam message string required
     * @param SmsRequest $request
     * @return ApiResource
     */
    public function storeAndSend(SmsRequest $request): ApiResource
    {
        $personId = $request->get('person_id');
        $message = $request->get('message');
        $senderId = $request->get('senderId');
        if ($personId !== null) {
            //get person primary phone
            $primaryAddress = $this->person::with('addresses')
                ->whereHas(
                    'addresses',
                    static function ($q) {
                        $q->where('is_primary', 1);
                    }
                )
                ->find($personId);
            $phone = $primaryAddress->addresses[0]->phone;
        } else {
            $phone = $request->get('phone');
        }

        $smsData = [
            'receiver' => $phone,
            'body' => $message,
            'direction' => 1,
            'sender_id' => $senderId,
        ];
        $sms = $this->smsService->createSms($smsData);
        $data = [
            'message' => $message,
            'phone' => $phone
        ];
        $this->smsService->sendSms($data, SmsTypes::MANUAL_SMS, SmsConfigs::class);
        return new ApiResource($sms);
    }

    /**
     * Marks the sms as sent
     *
     * @param string $uuid
     *
     * @return void
     */
    public function update($uuid): void
    {
        try {
            $sms = $this->sms->where('uuid', $uuid)->firstOrFail();
            $sms->status = 1;
            $sms->save();
        } catch (ModelNotFoundException $e) {
            Log::critical(
                'Sms confirmation failed ',
                [
                    'uuid' => $uuid,
                    'message' => 'the given uuid is not found in the database',
                    'id' => 'r4378563zjfhdfkjtwe-rtw423',
                ]
            );
        }
    }

    /**
     * Detail of Sms
     * @param $person_id
     * @responseFile responses/sms/sms.detail.json
     * @return ApiResource
     */
    public function show($person_id): ApiResource
    {
        $personAddresses = $this->person::with(
            [
                'addresses' => function ($q) {
                    $q->select(DB::raw('phone'), 'owner_id');
                },
            ]
        )
            ->where('id', $person_id)
            ->first();
        $numbers = $personAddresses->addresses->toArray();
        $smses = $this->sms::whereIn('receiver', $numbers)->orderBy('id', 'ASC')->get();
        return new ApiResource($smses);
    }

    /**
     * List of Sms By a Phone Number
     * @responseFile responses/sms/sms.list.by.number.json
     * @param $phone
     * @return ApiResource
     */
    public function byPhone($phone): ApiResource
    {
        $smses = $this->sms->where('receiver', $phone)->get();
        return new ApiResource($smses);
    }

    /**
     * Search Sms
     * @param $search
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search($search)
    {
        //search in people
        $list = $this->person::with('addresses')
            ->whereHas(
                'addresses',
                function ($q) use ($search) {
                    $q->where('phone', 'like', '%' . $search . '%')
                        ->where('is_primary', 1);
                }
            )
            ->orWhere('name', 'like', '%' . $search . '%')
            ->orWhere('surname', 'like', '%' . $search . '%')
            ->get();

        return SmsSearchResultResource::collection($list);
    }
}
