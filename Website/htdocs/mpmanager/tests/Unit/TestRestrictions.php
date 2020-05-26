<?php


namespace Tests\Unit;

use App\Http\Controllers\RestrictionController;
use App\Models\Restriction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class TestRestrictions extends TestCase
{

    private function bindRestrictionController()
    {
        $this->app->bind(RestrictionController::class, static function ($app) {
            $mock = new MockHandler([
                new Response(200, ['application/type' => 'JSON'], \GuzzleHttp\json_encode(['data' => 'mock-data']))
            ]);
            $handlerStack = HandlerStack::create($mock);
            $client = new Client(['handler' => $handlerStack]);


            return new RestrictionController(
                new Restriction(),
                $client
            );
        });
    }

    /**
     * @test
     */
    public function addMiniGrid()
    {
        $this->bindRestrictionController();

        // get the model to check if the limit of mini-grid has been changed
        $restriction =$this->app->make('Restriction');

        $oldLimit = $restrtion->where('target', 'enable-data-stream')->first();
        if($oldLimit === null) {

        }
        $response = $this->json('POST', '/api/restrictions', [
            'token' => 'test_token',
            'product_id' => 'product_id',
            'type' => 'mini-grid'
        ]);


        $response->assertStatus(201);




    }
}
