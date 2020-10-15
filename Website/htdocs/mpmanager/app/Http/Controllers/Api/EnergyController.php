<?php


namespace App\Http\Controllers;


use App\Http\Requests\StoreEnergyRequest;
use App\Http\Resources\ApiResource;
use App\Models\Energy;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @group Energies
 * Class EnergyController
 * @package App\Http\Controllers
 */
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

    /**
     * Create
     * @bodyParam mini_grid_id int required
     * @bodyParam node_id int required
     * @bodyParam device_id int required
     * @bodyParam meters int required
     * @bodyParam read_out int required
     * @param StoreEnergyRequest $request
     * @return ApiResource
     */
    public function store(StoreEnergyRequest $request): ApiResource
    {
        $meters = $request->get('meters');

        foreach ($meters as $meter) {
            $lastEnergyInput = $this->energy::query()->where('meter_id', $meter['id'])
                ->where('active', 1)
                ->latest()
                ->first();

            $totalEnergy = 0;
            $totalAbsorbedEnergy = 0;
            foreach ($meter['values'] as $value) {
                if ($value['name'] === 'Total yield') {
                    $totalEnergy = str_replace(array('.', ','), '', $value['value']);
                    break;
                }
                if ($value['name'] === 'Absorbed energy') {
                    $totalAbsorbedEnergy = str_replace(array('.', ','), '', $value['value']);
                }
            }

            if ($lastEnergyInput !== null) {
                $lastTotalEnergy = $lastEnergyInput->total_energy;
                $lastTotalAbsorbed = $lastEnergyInput->total_absorbed;
                $lastEnergyInput->active = 0;
                $lastEnergyInput->save();
            } else {
                $lastTotalEnergy = $totalEnergy;
                $lastTotalAbsorbed = $totalAbsorbedEnergy;
            }

            $usedEnergySinceLastInput = $totalEnergy - $lastTotalEnergy;
            $absorbedEnergySinceLastInput = $totalAbsorbedEnergy - $lastTotalAbsorbed;
            $this->energy->newQuery()->create([
                'meter_id' => $meter["id"],
                'active' => 1,
                'mini_grid_id' => $request->input('mini_grid_id'),
                'node_id' => $request->input('node_id'),
                'device_id' => $request->input('device_id'),
                'total_energy' => $totalEnergy,
                'read_out' => $request->input('read_out'),
                'used_energy_since_last' => $usedEnergySinceLastInput,
                'total_absorbed' => $totalAbsorbedEnergy,
                'absorbed_energy_since_last' => $absorbedEnergySinceLastInput,
            ]);

        }
        return new ApiResource(['result' => 'success']);
    }

    /**
     * Detail
     * Detail Energy of the specified mini grid
     * @urlParam miniGridId int
     * @param Request $request
     * @param $miniGridId
     * @return ApiResource
     */
    public function show(Request $request, $miniGridId): ApiResource
    {
        $energyReadings = $this->energy->newQuery()
            ->where('mini_grid_id', $miniGridId);

        if ($startDate = $request->input('start_date')) {
            $energyReadings->where('read_out', '>=',
                Carbon::createFromTimestamp($startDate)->format('Y-m-d H:i:s'));
        }
        if ($endDate = $request->input('end_date')) {

            $energyReadings->where('read_out', '<=',
                Carbon::createFromTimestamp($endDate)->format('Y-m-d H:i:s')
            );
        }
        return new ApiResource($energyReadings->get());
    }
}
