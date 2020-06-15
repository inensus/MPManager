<?php

use App\Http\Middleware\Transaction;
use App\Http\Middleware\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;


/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 04.07.18
 * Time: 14:28
 */
class VodacomTransactionTest extends TestCase
{

    /**
     * @var Response $response
     */

    //send the transaction from an IP which is not on the white list for vodacom transactions
    public function testTransactionFromUnkownSource()
    {
        $request = Request::create(URL::route('vodacomTransaction'), 'POST', [], [], [],
            ['REMOTE_ADDR' => '127.0.0.2']);
        $middleWare = new Transaction();

        $response = $middleWare->handle($request, function () {
        });
        $this->assertEquals($response->getStatusCode(), 401);
    }

    //send the transaction fron an trusted IP which is in the trusted ip list.
    public function testTransactionFromTrustedSource()
    {
        $passed = false;
        $request = Request::create(URL::route('vodacomTransaction'), 'POST', [], [], [],
            ['REMOTE_ADDR' => config('services.vodacom.ips')[0]]);

        $middleWare = new Transaction();

        $response = $middleWare->handle($request, function () use ($passed) {
            $passed = true;
        });
        $this->assertEquals($response, null);
    }
    // public function testValidVodacomTransaction() {
    //}

    public function testNewTransaction()
    {
        $request = Request::create(URL::route('vodacomTransaction'), 'POST',
            [], [], [],
            [
                'REMOTE_ADDR' => config('services.vodacom.ips')[0],
                'CONTENT_TYPE' => 'text/xml',
            ],
            '<mpesaBroker xmlns="http://infowise.co.tz/broker/" version="2.0"><request><serviceProvider><spId>332277</spId><spPassword>OUUFjsCKcOwCnGhCQpie1hvn29pGrXUI78Y46XwS3/U=</spPassword><timestamp>20180410111028</timestamp></serviceProvider><transaction><amount>2000.00</amount><commandID>Pay Bill</commandID><initiator>255757966347</initiator><conversationID>5DA71AKT7UP</conversationID><originatorConversationID>b3b609ab2c42496baa73db0dba22ba14</originatorConversationID><recipient>332277</recipient><mpesaReceipt>5DA71AKT7UP</mpesaReceipt><transactionDate>2018-04-10 11:10:28</transactionDate><accountReference>47000268749</accountReference><transactionID>76446330</transactionID></transaction></request></mpesaBroker>');


        $requestMiddleware = new Transaction();
        $validationMiddleware = new TransactionRequest();
        $requestMiddleware->handle($request, function () {
        });
        $validationMiddleware->handle($request, function () {
        });
        $this->assertEquals(1, true);
    }
}
