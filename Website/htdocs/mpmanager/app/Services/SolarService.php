<?php

namespace App\Services;

use App\Models\Solar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SolarService implements ISolarService
{
    public function create()
    {
        $solarData = request()->input('solar_reading');
        $frequency = request()->input('frequency');
        $pvPower = request()->input('pv_power');
        $miniGridId = request()->input('mini_grid_id');
        $deviceId = request()->input('device_id');
        $nodeId = request()->input('node_id');

        $solarRecord = [
            'node_id' => $nodeId,
            'device_id' => $deviceId,
            'mini_grid_id' => $miniGridId,
            'starting_time' => $solarData['starting_time'] ?? 0,
            'readings' => $solarData['readings'],
            'average' => (int)$solarData['average'],
            'min' => (int)($solarData['min'] ?? 0),
            'max' => (int)($solarData['max'] ?? 0),
            'duration' => $solarData['duration'] ?? 0,
            'ending_time' => $solarData['ending_time'] ?? 0,
            'time_stamp' => request()->input('time_stamp'),
            'frequency' => $frequency && $frequency > 0 ? $frequency : null,
            'pv_power' => $pvPower && $pvPower > 0 ? $pvPower : null,
        ];
        $solar = Solar::query()->create($solarRecord);
        $solar->fraction = round($this->findSlope($miniGridId, $nodeId, $deviceId), 5);

        $solar->save();
        return $solar;
    }

    /**
     * @return Builder[]|Collection
     *
     * @psalm-return \Illuminate\Database\Eloquent\Collection|array<array-key, Builder>
     */
    public function list()
    {
        $solarReadings = $this->filter(Solar::query());

        return $solarReadings->get();
    }

    /**
     * @return Builder[]|Collection
     *
     * @psalm-return \Illuminate\Database\Eloquent\Collection|array<array-key, Builder>
     */
    public function lisByMiniGrid(int $miniGridId)
    {
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $limit = request()->input('per_page');
        $withWeather = request()->input('weather');

        $query = Solar::query()->where('mini_grid_id', '=', $miniGridId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($withWeather) {
            $query->with('weatherData');
        }

        if ($limit) {
            $query->take($limit);
        }

        return $query->get();
    }

    /**
     * @return Builder|Model|null
     */
    public function showByMiniGrid(int $miniGridId)
    {
        return Solar::query()->where('mini_grid_id', $miniGridId)->first();
    }


    private function filter(Builder $query): Builder
    {
        if ($startDate = request()->input('start_date')) {
            $query->where(
                'time_stamp',
                '>=',
                Carbon::createFromTimestamp($startDate)->format('Y-m-d H:i:s')
            );
        }
        if ($endDate = request()->input('end_date')) {
            $query->where(
                'time_stamp',
                '<=',
                Carbon::createFromTimestamp($endDate)->format('Y-m-d H:i:s')
            );
        }
        if ($limit = request()->input('limit')) {
            $query->take($limit);
        }
        if (request()->input('weather')) {
            $query->with('weatherData');
        }
        return $query;
    }

    // find slope of 2 arrays
    private function findSlope($miniGridId, $nodeId, $deviceId)
    {

        $query = Solar::query()
            ->where('mini_grid_id', $miniGridId)
            ->where('node_id', $nodeId)
            ->where('device_id', $deviceId)
            ->whereNotNull('pv_power')
            ->whereNotNull('frequency')
            ->where('frequency', '<=', 50000)->get();

        $pvPowers = $query->pluck('pv_power')->toArray();
        $solarReadings = $query->pluck('average')->toArray();

        $x = $solarReadings;
        $y = $pvPowers;

        if (count($x) && count($y)) {
            $n = count($x);
            $sum_x = 0;
            $sum_y = 0;
            $sum_xy = 0;
            $sum_xx = 0;
            $sum_yy = 0;

            for ($i = 0; $i < $n; $i++) {
                $sum_x += $x[$i];
                $sum_y += $y[$i];
                $sum_xy += ($x[$i] * $y[$i]);
                $sum_xx += ($x[$i] * $x[$i]);
                $sum_yy += ($y[$i] * $y[$i]);
            }
            return ($n * $sum_xy - $sum_x * $sum_y) / ($n * $sum_xx - $sum_x * $sum_x);
        }
        return 0;
    }
}
