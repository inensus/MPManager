<?php

namespace App\Sms\Senders;

class ManualSms extends SmsSender
{
    protected $data;
    public $body = '';
    protected $references = [
        'header' => null,
        'body' => null,
        'footer' => null
    ];

    public function prepareHeader()
    {
        $this->body .= '';
    }
    public function prepareBody()
    {
        $this->body .= $this->data['message'];
    }
    public function prepareFooter()
    {
        $this->body .= '';
    }
}
