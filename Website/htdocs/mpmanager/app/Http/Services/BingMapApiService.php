<?php


namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BingMapApiService
{
    private $url = 'https://dev.virtualearth.net/REST/v1/Imagery/Metadata/Aerial?key=';

    public function checkBingApiKey($key)
    {
        $client = new Client();

        try{

            $response = $client->get($this->url . $key);

            return json_decode((string)$response->getBody(), true);

        }catch (GuzzleException $e){

            return json_decode((string)$e->getMessage(), true);

        }

    }
}
