<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreEnergyRequest;
use App\Http\Resources\ApiResource;
use App\Models\Energy;

class EnergyController extends Controller
{
    /**
     * @var Energy
     */
    private $energy;

    public function __construct(Energy $energy)
    {
        $this->energy = $energy;
    }

    public function store(StoreEnergyRequest $request): ApiResource
    {
        $meters = $request->get('meters');

        foreach ($meters as $meter) {
            $last_energy_input = $this->energy::where('meter_id', $meter['id'])
                ->where('active', 1)
                ->latest()
                ->first();

            $total_energy = 0;
            foreach ($meter['values'] as $value) {
                if ($value['name'] === 'Total yield') {
                    $total_energy = str_replace(array('.', ','), '', $value['value']);
                    break;
                }
            }

            if ($last_energy_input !== null) {

                $last_total_energy = $last_energy_input->total_energy;
                $last_energy_input->active = 0;
                $last_energy_input->save();
            } else {
                $last_total_energy = $total_energy;
            }

            $used_energy_since_last_input = $total_energy - $last_total_energy;
            $this->energy->create([
                'meter_id' => $meter["id"],
                'active' => 1,
                'mini_grid_id' => $request->get('mini_grid_id'),
                'node_id' => $request->get('node_id'),
                'device_id' => $request->get('device_id'),
                'total_energy' => $total_energy,
                'read_out' => date('Y-m-d H:i:s', $request->get('read_out')),
                'used_energy_since_last' => $used_energy_since_last_input,
            ]);

        }
        return new ApiResource(['result' => 'success']);
    }
}
