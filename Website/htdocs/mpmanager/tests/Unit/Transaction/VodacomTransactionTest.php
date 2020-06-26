<?php

use App\Http\Middleware\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    use RefreshDatabase, WithFaker;
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
        $this->assertEquals($response->status(), 401);
    }

    //send the transaction fron an trusted IP which is in the trusted ip list.
    public function testTransactionFromTrustedSource()
    {
        $request = Request::create(URL::route('vodacomTransaction'), 'POST', [], [], [],
            ['REMOTE_ADDR' => config('services.vodacom.ips')[0]]);

        $middleWare = new Transaction();

        $response = $middleWare->handle($request, function () {
        });
        $this->assertEquals($response, null);
    }


}
