<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\City;
use App\Models\Cluster;
use App\Models\MiniGrid;
use App\Models\SubTarget;
use App\Models\Target;
use Illuminate\Http\Request;

/**
 * @group   Target
 * Class TargetController
 * @package App\Http\Controllers
 */
class TargetController extends Controller
{
    /**
     * @var Target
     */
    private $target;
    /**
     * @var SubTarget
     */
    private $subTarget;
    /**
     * @var Cluster
     */
    private $cluster;
    /**
     * @var City
     */
    private $miniGrid;

    public function __construct(Target $target, SubTarget $subTarget, Cluster $cluster, MiniGrid $miniGrid)
    {
        $this->target = $target;
        $this->subTarget = $subTarget;
        $this->cluster = $cluster;
        $this->miniGrid = $miniGrid;
    }


    /**
     * List of all Targets.
     * A list of the all targets.
     * @responseFile responses/target/targets.list.json
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $targets = $this->target::with('owner', 'subTargets.connectionType')->orderBy(
            'target_date',
            'desc'
        )->paginate(15);
        return new ApiResource($targets);
    }

    /**
     * Details of the specified target.
     * @urlParam targetId required.
     * @responseFile responses/target/target.detail.json
     * @param $id
     * @return ApiResource
     */
    public function show($id): ApiResource
    {
        $target = $this->target->with('subTargets', 'city')->find($id);
        return new ApiResource($target);
    }

    /**
     * Target Slots
     * @return ApiResource
     */
    public function getSlotsForDate(): ApiResource
    {
        $date = request('date');
        $lastDayOfMonth = date('Y-m-t', strtotime($date));
        $firstDayOfMonth = date('Y-m-1', strtotime($date));

        //get all targets in range
        $takenSlots = $this->target::whereBetween('target_date', [$firstDayOfMonth, $lastDayOfMonth])->get();

        return new ApiResource($takenSlots);
    }

    /**
     * Create a new Target.
     * @bodyParam data string
     * @bodyParam period string
     * @bodyParam targetId int
     * @bodyParam targetType string
     * @param Request $request
     */
    public function store(Request $request)
    {
        $data = $request->get('data');
        $period = $request->get('period');
        $targetId = $request->get('targetId') ?? 1;
        $targetType = $request->get('targetType') ?? 'weekly';
        if ($data === null || $period === null) {
            return;
        }

        if ($targetType === "cluster") {
            $targetOwner = $this->cluster->find($targetId);
        } else {
            $targetOwner = $this->miniGrid->find($targetId);
        }

        $period = date('Y-m-d', strtotime($period));

        //save target
        $target = $this->target->create();
        $target->owner()->associate($targetOwner);
        $target->target_date = $period;
        $target->type = $targetType;
        $target->save();


        foreach ($data as $subTargetData) {
            $subTarget = $this->subTarget->create();
            $subTarget->target_id = $target->id;

            $subTarget->revenue = $subTargetData['target']['totalRevenue'];
            $subTarget->connection_id = $subTargetData['id'];
            $subTarget->new_connections = $subTargetData['target']['newConnection'];
            $subTarget->connected_power = $subTargetData['target']['connectedPower'];
            $subTarget->energy_per_month = $subTargetData['target']['energyPerMonth'];
            $subTarget->average_revenue_per_month = $subTargetData['target']['averageRevenuePerMonth'];

            $subTarget->save();
        }        //create sub targets

        return $target;
    }
}
