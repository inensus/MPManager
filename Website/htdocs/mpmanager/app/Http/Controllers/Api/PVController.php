<?php


namespace App\Http\Controllers;


use App\Helpers\PowerConverter;
use App\Http\Requests\PVRequest;
use App\Http\Resources\ApiResource;
use App\Models\PV;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group PV
 * Class PVController
 * @package App\Http\Controllers
 */
class PVController extends Controller
{
    /**
     * @var
     */
    private $pv;


    public function __construct(PV $pv)
    {
        $this->pv = $pv;
    }


    public function showReadings(Request $request, $miniGridId)
    {
        $pvReadings = $this->pv->newQuery()
            ->where('mini_grid_id', $miniGridId);

        if ($startDate = $request->input('start_date')) {
            $pvReadings->where('reading_date', '>=',
                Carbon::createFromTimestamp($startDate)->format('Y-m-d H:i:s'));
        }
        if ($endDate = $request->input('end_date')) {

            $pvReadings->where('reading_date', '<=',
                Carbon::createFromTimestamp($endDate)->format('Y-m-d H:i:s')
            );
        }
        return new ApiResource($pvReadings->get());
    }

    /**
     * Create
     * @param PVRequest $request
     * @param Response $response
     * @bodyParam mini_grid_id int required
     * @bodyParam node_id int required
     * @bodyParam device_id int required
     * @bodyParam pv array required
     * @return Response|void
     */
    public function store(PVRequest $request, Response $response)
    {

        $miniGridId = $request->get('mini_grid_id');
        $nodeId = $request->get('node_id');
        $pv = $request->get('pv');

        if (!array_key_exists('daily', $pv) || !array_key_exists('total', $pv)) {
            return $response->setStatusCode(422)->setContent([
                'data' => [
                    'message' => 'daily , total are required',
                    'status_code' => 422
                ]
            ]);
        }

        $daily_gen_energy = (double)(str_replace(',', '.', $pv['daily']['energy']));
        $total_gen_energy = (double)(str_replace(',', '.', $pv['total']['energy']));


        $this->pv::create([
            'mini_grid_id' => $request->get('mini_grid_id'),
            'node_id' => $request->get('node_id'),
            'device_id' => $request->get('device_id'),
            'reading_time' => $request->get('time_stamp'),
            'daily' => $daily_gen_energy,
            'daily_unit' => $pv['daily']['unit'],
            'total' => $total_gen_energy,
            'total_unit' => $pv['total']['unit'],
            'new_generated_energy' => 0,
            'new_generated_energy_unit' => 'Wh'
        ]);

        return;
    }

    /**
     * List for Mini-Grid
     * @urlParam limit int Default = 50
     * @param $miniGridId
     * @param Request $request
     * @return ApiResource
     */
    public function show($miniGridId, Request $request)
    {
        $limit = $request->get('limit') ?? 50;
        $miniGridPVs = $this->pv->where('mini_grid_id', $miniGridId)->latest()->take($limit)->get()->reverse();

        foreach ($miniGridPVs as $index => $miniGridPV) {
            $miniGridPVs[$index]['daily'] = PowerConverter::convert($miniGridPV->daily, $miniGridPV->daily_unit, 'kWh');
            $miniGridPVs[$index]['daily_unit'] = 'kWh';

            $miniGridPVs[$index]['new_generated_energy'] = PowerConverter::convert($miniGridPV->new_generated_energy,
                $miniGridPV->new_generated_energy_unit, 'kWh');

            $miniGridPVs[$index]['new_generated_energy_unit'] = 'kWh';
        }
        return new ApiResource($miniGridPVs);

    }
}
