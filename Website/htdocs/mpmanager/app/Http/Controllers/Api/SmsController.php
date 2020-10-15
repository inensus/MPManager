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

use App\Models\ConnectionGroup;
use App\Models\ConnectionType;
use App\Models\Meter\MeterParameter;

use App\Models\Person\Person;
use App\Models\Sms;
use App\Models\Transaction\Transaction;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inensus\Ticket\Services\CommentService;
use Inensus\Ticket\Trello\Api;
use Inensus\Ticket\Trello\Comments;

/**
 * @group Sms
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

    /**
     * SmsController constructor.
     *
     * @param Sms $sms
     * @param Person $person
     * @param ConnectionGroup $connectionGroup
     * @param ConnectionType $connectionType
     */
    public function __construct(
        Sms $sms,
        Person $person,
        ConnectionGroup $connectionGroup,
        ConnectionType $connectionType,
        MeterParameter $meterParameter
    ) {
        $this->sms = $sms;
        $this->person = $person;
        $this->connectionGroup = $connectionGroup;
        $this->connectionType = $connectionType;
        $this->meterParameter = $meterParameter;
    }

    /**
     * List
     * @return ApiResource
     */

    public function index()
    {

        $list = $this->sms
            ::with('address.owner')
            ->orderBy('id', 'DESC')
            ->groupBy('receiver')
            ->paginate(20);
        return new ApiResource($list);
    }

    /**
     * Create Bulk
     *
     * @param Request $request
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

                $this->sms->create([
                    'receiver' => $phone,
                    'body' => $message,
                    'direction' => 1,
                    'sender_id' => $senderId,
                ]);

                resolve('SmsProvider')
                    ->sendSms(
                        $phone,
                        $message,
                        'manual'
                    );
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
                    ])
                    ->whereHas('address', static function ($q) use ($miniGrid) {
                        if ((int)$miniGrid === 0) {
                            $q->where('city_id', '>', 0);
                        } else {
                            $q->where('city_id', $miniGrid);
                        }
                    })->whereHas('connectionGroup', function ($q) use ($receivers) {
                        $q->where('id', $receivers);
                    })->get();
            } elseif ($type === 'all') {

                $meters = $this->meterParameter::with(
                    [
                        'owner.addresses' =>
                            static function ($q) {
                                $q->where('is_primary', 1);
                            },
                    ])
                    ->whereHas('address', static function ($q) use ($miniGrid) {
                        if ((int)$miniGrid === 0) {
                            $q->where('city_id', '>', 0);
                        } else {
                            $q->where('city_id', $miniGrid);
                        }
                    })->get();
            } else {
                $meters = $this->meterParameter::with(
                    [
                        'owner.addresses' =>
                            static function ($q) {
                                $q->where('is_primary', 1);
                            },
                    ])
                    ->whereHas('address', static function ($q) use ($miniGrid) {
                        if ((int)$miniGrid === 0) {
                            $q->where('city_id', '>', 0);
                        } else {
                            $q->where('city_id', $miniGrid);
                        }
                    })->whereHas('connectionType', function ($q) use ($receivers) {
                        $q->where('id', $receivers);
                    })->get();
            }


            $addresses = $meters->pluck('owner.addresses');
            foreach ($addresses as $address) {
                if ($address === null) {
                    continue;
                }
                $this->sms->create([
                    'receiver' => $address[0]->phone,
                    'body' => $message,
                    'direction' => 1,
                    'sender_id' => $senderId,
                ]);

                resolve('SmsProvider')
                    ->sendSms(
                        $address[0]->phone,
                        $message,
                        'manual'
                    );
            }
        }
    }

    /**
     * Create
     * Create a new sms
     * @bodyParam sender int required
     * @bodyParam message string required
     * @param StoreSmsRequest $request
     * @return ApiResource
     */
    public function store(StoreSmsRequest $request): ApiResource
    {
        $sender = $request->get('sender');
        $message = $request->get('message');

        $sms = $this->sms->create([
            'receiver' => $sender,
            'body' => $message,
            'direction' => 0,
            'sender_id' => null,
        ]);

        if (stripos($message, 'luku') === 0) {
            //get last generated token
            preg_match('!\d+!', $message, $match);

            if (count($match) === 1) {
                $meterSerial = $match[0];
                $smsBody = 'Malipo yako ya Mwisho ni ';
                try {
                    $t = Transaction::with('paymentHistories')->where('message', $meterSerial)->latest()->firstOrFail();
                } catch (ModelNotFoundException $ex) {
                    resolve('SmsProvider')
                        ->sendSms(
                            $sender,
                            "Namba ya mita uliyoandika siyo sahihi. Tafadhali hakikisha namba yako ya mita.",
                            'demand'
                        );
                    return new ApiResource([
                        'success' => 'false',
                        'message' => 'given serial number was not found in the system'
                    ]);
                }
                $smsBody .= ' ' . $t->amount . ' at ' . $t->created_at . ' ';

                foreach ($t->paymentHistories as $paymentHistory) {
                    $smsBody .= '* ' . $paymentHistory->amount . 'TZS for ' . $paymentHistory->payment_type . ' ';
                    if (get_class($paymentHistory->paidFor) === 'App\\Models\\Meter\\MeterToken') {
                        $smsBody .= 'LUKU ya Umeme(' . $paymentHistory->paidFor->token . ') ya ' . $paymentHistory->paidFor->energy . 'kWH.';
                    }
                }

            }
            resolve('SmsProvider')
                ->sendSms(
                    $sender,
                    $smsBody,
                    'demand'
                );

            return new ApiResource($sms);
        }


        // store a comment if the sender is an maintenance guy  and responds with sms to an open ticket.
        $person = $this->person::with([
            'addresses',
            'tickets' => static function ($q) {
                $q->where('status', 0)->latest()->limit(1);
            },
        ])
            ->whereHas('addresses', static function ($q) use ($sender) {
                $q->where('phone', $sender);
            })
            ->where('is_customer', 0)
            ->first();

        if ($person && !$person->tickets->isEmpty()) {
            $cS = new CommentService(new Comments(new Api(new Client())));
            $cS->createComment($person->tickets[0]->ticket_id, 'Sms Comment' . $message);
        }


        return new ApiResource($sms);

    }

    /**
     * Create and Send
     * @bodyParam message string required
     * @bodyParam person_id int required
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
                ->whereHas('addresses', static function ($q) {
                    $q->where('is_primary', 1);
                })
                ->find($personId);
            $phone = $primaryAddress->addresses[0]->phone;
        } else {
            $phone = $request->get('phone');
        }

        //$phone = str_replace('+', '', $phone);

        $sms = $this->sms->create([
            'receiver' => $phone,
            'body' => $message,
            'direction' => 1,
            'sender_id' => $senderId,
        ]);
        resolve('SmsProvider')
            ->sendSms(
                $phone,
                $message,
                'manual'
            );
        return new ApiResource($sms);
    }

    /**
     * Marks the sms as sent
     *
     * @param Sms $sms
     */
    public function update($uuid)
    {
        Log::critical('Sms confirmation failed ', [
            'uuid' => $uuid,
            'content' => request()->getContent(),
        ]);
        try {
            $sms = $this->sms->where('uuid', $uuid)->firstOrFail();
            $sms->status = 1;
            $sms->save();
        } catch (ModelNotFoundException $e) {
            Log::critical('Sms confirmation failed ', [
                'uuid' => $uuid,
                'message' => 'the given uuid is not found in the database',
                'id' => 'r4378563zjfhdfkjtwe-rtw423',
            ]);
        }
    }

    /**
     * Detail
     * Sms Detail of the specified person
     * @urlParam person_id int required
     * @param $person_id
     * @return ApiResource
     */
    public function show($person_id)
    {
        $personAddresses = $this->person::with([
            'addresses' => function ($q) {
                $q->select(DB::raw('phone'), 'owner_id');
            },
        ])
            ->where('id', $person_id)
            ->first();

        $numbers = $personAddresses->addresses->toArray();


        $smses = $this->sms::whereIn('receiver', $numbers)->orderBy('id', 'ASC')->get();
        return new ApiResource($smses);
    }

    /**
     * List by Phone
     * @urlParam phone int required
     * @param $phone
     * @return ApiResource
     */
    public function byPhone($phone)
    {
        $smses = $this->sms->where('receiver', $phone)->get();
        return new ApiResource($smses);
    }


    /**
     * Search
     *
     * @param $search
     * @return ApiResource
     */
    public function search($search)
    {
        //search in people
        $list = $this->person::with('addresses')
            ->whereHas('addresses', function ($q) use ($search) {
                $q->where('phone', 'like', '%' . $search . '%')
                    ->where('is_primary', 1);
            })
            ->orWhere('name', 'like', '%' . $search . '%')
            ->orWhere('surname', 'like', '%' . $search . '%')
            ->get();

        return new ApiResource($list);
    }


}
