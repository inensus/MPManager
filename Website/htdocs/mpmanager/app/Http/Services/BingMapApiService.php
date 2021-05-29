<?php


namespace App\Http\Services;

use App\Exceptions\BingApiUnauthorized;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BingMapApiService
{
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function checkAuthenticationKey($key)
    {
        try {
            $response = $this->httpClient->get(config('services.bingApiURL') . $key);
        } catch (GuzzleException $e) {
            throw new BingApiUnauthorized();
        }
        return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
