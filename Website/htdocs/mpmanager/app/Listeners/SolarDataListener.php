<?php

namespace App\Listeners;


use App\Exceptions\WeatherProviderUnreachable;
use App\Models\MiniGrid;
use App\Models\Solar;
use App\Models\WeatherData;
use App\Services\IWeatherDataProvider;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psy\Exception\ErrorException;


/**
 * Class SolarDataListener
 * @package App\Listeners
 * @Kemal
 * TODO: replace that listener with an model observer and fetch the data on a separate job that is triggered by "created" method .
 */
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

    /**
     * @param Solar $solar
     * @param int $miniGridId
     * @throws WeatherProviderUnreachable
     */
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


        $currentWeather = $this->weatherDataProvider->getCurrentWeatherData($miniGridPoints);
        if ($currentWeather->getStatusCode() !== 200) {
            throw new WeatherProviderUnreachable('Current weather data is not available ' . (string)$currentWeather->getBody(),
                $currentWeather->getStatusCode());
        }
        $currentWeatherData = $currentWeather->getBody();


        $forecastWeather = $this->weatherDataProvider->getWeatherForeCast($miniGridPoints);
        if ($forecastWeather->getStatusCode() !== 200) {
            throw new WeatherProviderUnreachable('Current weather data is not available ' . (string)$forecastWeather->getBody()
            ,$forecastWeather->getStatusCode());

        }
        $forecastWeatherData = $forecastWeather->getBody();

        $date = Carbon::parse($solar->time_stamp);
        $currentWeatherFileName = 'current-' . $solar->node_id . $solar->device_id . $date->timestamp . '.json';
        $forecastWeatherFileName = 'forecast-' . $solar->node_id . $solar->device_id . $date->timestamp . '.json';
        $this->weatherData::create([
            'solar_id' => $solar->id,
            'current_weather_data' => $currentWeatherFileName,
            'forecast_weather_data' => $forecastWeatherFileName,
            'record_time' => $date->timestamp,
        ]);

        $this->storeWeatherData($currentWeatherFileName, (string)$currentWeatherData);
        $this->storeWeatherData($forecastWeatherFileName, (string)$forecastWeatherData);
    }


    private function storeWeatherData($fileName, $data)
    {

        Storage::disk('local')->put('solar-reading/' . $fileName, $data);
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
