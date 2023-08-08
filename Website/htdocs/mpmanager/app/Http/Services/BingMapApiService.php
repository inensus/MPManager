<?php

namespace App\Http\Services;

use App\Exceptions\InvalidBingApiKeyException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BingMapApiService
{

    public function __construct(private Client $httpClient)
    {
    }

    /**
     * @throws InvalidBingApiKeyException
     */
    public function checkAuthenticationKey($key): bool
    {
        try {
            $response = $this->httpClient->get(config('services.bingMapApi.url') . $key)->getBody()->getContents();
        } catch (GuzzleException $e) {
            throw new InvalidBingApiKeyException($e->getMessage());
        }
        return $this->isRequestAuthenticated($response);
    }

    private function isRequestAuthenticated($body): bool
    {
        try {
            $jsonBody = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            $authenticated = $jsonBody['statusDescription'] === "OK";
        } catch (Exception $e) {
            unset($e);
            $authenticated = false;
        }
        return $authenticated;
    }
}
