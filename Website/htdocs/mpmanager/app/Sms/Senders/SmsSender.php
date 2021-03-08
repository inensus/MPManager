<?php

namespace App\Sms\Senders;

use App\Exceptions\MissingSmsReferencesException;
use App\Jobs\SmsLoadBalancer;
use App\Models\AssetRate;
use App\Models\Transaction\Transaction;
use App\Services\SmsBodyService;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;

abstract class SmsSender
{
    protected $smsBodyService;
    protected $data;
    protected $references;
    protected $body = '';
    protected $receiver;
    protected $callback;

    public function __construct($data, SmsBodyService $smsBodyService)
    {
        $this->smsBodyService = $smsBodyService;
        $this->data = $data;
        $this->validateReferences();
        $this->prepareHeader();
        $this->prepareBody();
        $this->prepareFooter();
    }

    public function sendSms()
    {
        if (config('app.debug')) {
            Log::debug(
                'Send sms on debug is not allowed in debug mode',
                ['number' => $this->receiver, 'message' => $this->body]
            );
            return;
        }
        if (!($this->data instanceof Transaction) && !($this->data instanceof AssetRate)) {
            $nullSmsBodies = $this->smsBodyService->getNullBodies();
            if (count($nullSmsBodies)) {
                Log::debug('Send sms rejected, some of sms bodies are null', ['Sms Bodies' => $nullSmsBodies]);
                return;
            }
        }
        //add sms to sms_gateway
        resolve('SmsProvider')
            ->sendSms(
                $this->receiver,
                $this->body,
                $this->callback
            );
    }

    public function prepareHeader()
    {
        $smsBody = $this->smsBodyService->getSmsBodyByReference($this->references['header']);
        $className = 'App\\Sms\\BodyParsers\\' . $this->references['header'];
        $smsObject = new  $className($this->data);
        $this->body .= $smsObject->parseSms($smsBody->body);
    }

    public function prepareBody()
    {

        $smsBody = $this->smsBodyService->getSmsBodyByReference($this->references['body']);
        $className = 'App\\Sms\\BodyParsers\\' . $this->references['body'];
        $smsObject = new $className($this->data);
        $this->body .= $smsObject->parseSms($smsBody->body);
    }

    public function prepareFooter()
    {
        $smsBody = $this->smsBodyService->getSmsBodyByReference($this->references['footer']);
        $this->body .= $smsBody->body;
    }

    private function validateReferences()
    {
        if (
            !array_key_exists('header', $this->references) ||
            !array_key_exists('body', $this->references) ||
            !array_key_exists('footer', $this->references)
        ) {
            throw  new MissingSmsReferencesException('header, body & footer keys must be defined in references array');
        }
    }

    public function getReceiver()
    {
        if ($this->data instanceof Transaction) {
            $this->receiver = strpos($this->data->sender, '+') === 0 ? $this->data->sender : '+' . $this->data->sender;
        } elseif ($this->data instanceof AssetRate) {
            $this->receiver = strpos(
                $this->data->assetPerson->person->addresses->first()->phone,
                '+'
            ) === 0 ? $this->data->assetPerson->person->addresses
                ->first()->phone : '+' . $this->data->assetPerson->person->addresses->first()->phone;
        } else {
            $this->receiver = strpos(
                $this->data['phone'],
                '+'
            ) === 0 ? $this->data['phone'] : '+' . $this->data['phone'];
        }
        return $this->receiver;
    }

    public function generateCallback()
    {
        $uuid = (string)Uuid::generate(4);
        if (!($this->data instanceof Transaction) && ($this->data instanceof AssetRate)) {
            $this->callback = 'manual';
        }
        $this->callback = sprintf(config()->get('services.sms.callback'), $uuid);
        return $uuid;
    }
}
