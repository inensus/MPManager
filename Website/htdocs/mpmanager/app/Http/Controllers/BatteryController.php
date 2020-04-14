<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreBatteryStateRequest;
use App\Http\Resources\ApiResource;
use App\Models\Battery;
use Illuminate\Http\Request;
use Inensus\Ticket\Trello\Api;

/**
 * @group Battery
 * Class BatteryController
 * @package App\Http\Controllers
 */
class BatteryController extends Controller
{


    /**
     *
     * @var Battery
     */
    private $battery;

    public function __construct(Battery $battery)
    {
        $this->battery = $battery;
    }

    /**
     * Battery details for Mini-Grid
     * @urlParam miniGridId int required
     * @urlParam limit int Default 50
     *
     * @param Request $request
     * @param $id
     * @return ApiResource
     */
    public function showByMiniGrid(Request $request, $id): ApiResource
    {
        $limit = $request->get('limit') ?? 50;
        $batteries = $this->battery->where('mini_grid_id', $id)
            ->latest()
            ->take($limit)
            ->get();
        return new ApiResource($batteries);
    }

    /**
     * Store battery status
     * @urlParam miniGridId integer required
     * @param StoreBatteryStateRequest $request
     * @return ApiResource
     */
    public function store($miniGridId, StoreBatteryStateRequest $request): ApiResource
    {
        $battery_data = $request->get('batteries');
        $state_of_charge_data = $battery_data['state_of_charge'];
        $state_of_health_data = $battery_data['state_of_health'];
        $total = $battery_data['battery_discharge'];

        $battery = $this->battery::create([
            'mini_grid_id' => $request->get('mini_grid_id'),
            'node_id' => $request->get('node_id'),
            'device_id' => $request->get('device_id'),
            'battery_count' => $state_of_charge_data['count'],
            'soc_average' => $state_of_charge_data['average'],
            'soc_unit' => $state_of_charge_data['unit'],
            'soc_min' => $state_of_charge_data['min'] ?? 0,
            'soc_max' => $state_of_charge_data['max'] ?? 0,

            'soh_average' => 100 - (double)$state_of_health_data['average'],
            'soh_unit' => $state_of_health_data['unit'],
            'soh_min' => 100 - (double)($state_of_health_data['min'] ?? 0),
            'soh_max' => 100 - (double)($state_of_health_data['max'] ?? 0),

            'd_total' => str_replace(',', '.', $total['discharge']),
            'd_total_unit' => $total['unit'],
            'd_newly_energy' => 0,
            'd_newly_energy_unit' => 'Wh',

            'read_out' => $request->get('read_out'),
        ]);


        return new ApiResource($battery);
    }
}
