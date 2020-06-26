<?php

namespace App\Services;

use App\Exceptions\WeatherParametersMissing;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use function config;

class OpenWeatherMap implements IWeatherDataProvider
{
    protected $apiKey;
    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->apiKey = config('services.weather.owm_app_id');
        Log::debug('api key', [$this->apiKey]);
        $this->httpClient = $httpClient;
    }

    /**
     * @param array $geoLocation
     * @return ResponseInterface
     * @throws WeatherParametersMissing
     */
    public function getCurrentWeatherData(array $geoLocation): ResponseInterface
    {
        if (count($geoLocation) !== 2) {
            throw new WeatherParametersMissing('Lat and Lon should provided to get weather data');
        }
        return $this->httpClient->get('https://api.openweathermap.org/data/2.5/weather?APPID=' . $this->apiKey . '&units=metric&lat=' . $geoLocation[0] . '&lon=' . $geoLocation[1]);
    }

    /**
     * @param array $geoLocation
     * @return ResponseInterface
     * @throws WeatherParametersMissing
     */
    public function getWeatherForeCast(array $geoLocation): ResponseInterface
    {
        if (count($geoLocation) !== 2) {
            throw new WeatherParametersMissing('Lat and Lon should provided to get weather data');
        }
        return $this->httpClient->get('https://api.openweathermap.org/data/2.5/forecast?APPID=' . $this->apiKey . '&units=metric&lat=' . $geoLocation[0] . '&lon=' . $geoLocation[1]);
    }
}
