<?php


namespace Tests\Unit;

use App\Http\Controllers\RestrictionController;
use App\Models\Restriction;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestrictionsTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    private function bindRestrictionController(): void
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
    public function addMiniGrid(): void
    {
        $this->bindRestrictionController();

        // get the model to check if the limit of mini-grid has been changed
        $restriction = $this->app->make(Restriction::class);
        $oldLimit = $restriction->where('target', 'enable-data-stream')->first();

        $response = $this->json('POST', '/api/restrictions', [
            'token' => 'test_token',
            'product_id' => 'product_id',
            'type' => 'mini-grid'
        ]);
        $newLimit = $restriction->where('target', 'enable-data-stream')->first();

        $response->assertStatus(201);
        $this->assertNotEquals($oldLimit->limit, $newLimit->limit);




    }
}
