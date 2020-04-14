<?php

namespace App\Listeners;


use App\Exceptions\WeatherProviderUnreachable;
use App\Lib\IWeatherDataProvider;
use App\Models\MiniGrid;
use App\Models\Solar;
use App\Models\WeatherData;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;

class  SolarDataListener
{
    /**
     * @var IWeatherDataProvider
     */
    private $weatherDataProvider;
    /**
     * @var MiniGrid
     */
    private $miniGrid;

    /**
     * @var WeatherData
     */
    private $weatherData;

    public function __construct(
        WeatherData $weatherData,
        IWeatherDataProvider $weatherDataProvider,
        MiniGrid $miniGrid
    ) {
        $this->weatherData = $weatherData;
        $this->weatherDataProvider = $weatherDataProvider;
        $this->miniGrid = $miniGrid;
    }

    public function onSolarReading(Solar $solar, int $miniGridId): void
    {
        try {
            $miniGridPoints = $this->getMiniGridLocation($miniGridId);
        } catch (ModelNotFoundException $x) {
            Log::critical("Weather forecast reading failed for minigrid: $miniGridId. $miniGridId does not exist in the database");
            return;
        } catch (ErrorException $x) {
            Log::critical("Weather forecast reading failed for minigrid: $miniGridId. Reading of geo location points failed");
            return;
        }

        Log::debug('Points', $miniGridPoints);

        $currentWeather = $this->weatherDataProvider->getCurrentWeatherData($miniGridPoints);
        if ($currentWeather->getStatusCode() === 200) {
            $currentWeatherData = $currentWeather->getBody();
        } else {
            throw new WeatherProviderUnreachable('Current weather data is not available',
                (string)$currentWeather->getBody());
        }

        $forecastWeather = $this->weatherDataProvider->getWeatherForeCast($miniGridPoints);
        if ($forecastWeather->getStatusCode() === 200) {
            $forecastWeatherData = $forecastWeather->getBody();
        } else {
            throw new WeatherProviderUnreachable('Current weather data is not available',
                (string)$forecastWeather->getBody());
        }

        $currentWeatherFileName = 'current-' . $solar->node_id . $solar->device_id . $solar->time_stamp . '.json';
        $forecastWeatherFileName = 'forecast-' . $solar->node_id . $solar->device_id . $solar->time_stamp . '.json';
        $this->weatherData::create([
            'solar_id' => $solar->id,
            'current_weather_data' => $currentWeatherFileName,
            'forecast_weather_data' => $forecastWeatherFileName,
            'record_time' => $solar->time_stamp,
        ]);
        Storage::disk('local')->put('solar-reading/' . $currentWeatherFileName, (string)$currentWeatherData);
        Storage::disk('local')->put('solar-reading/' . $forecastWeatherFileName, (string)$forecastWeatherData);


    }

    private function getMiniGridLocation(int $miniGridId): array
    {
        $miniGrid = $this->miniGrid
            ::with('location')
            ->findOrFail($miniGridId);

        return explode(',', $miniGrid->location->points);
    }


    public function subscribe(Dispatcher $events): void
    {
        $events->listen('solar.received', '\App\Listeners\SolarDataListener@onSolarReading');
    }
}
