<?php

namespace App\Observers;

use App\Models\GeographicalInformation;
use App\Models\MiniGrid;
use App\Models\ClusterMetaData;

class MiniGridObserver
{
    /**
     * @var GeographicalInformation
     */
    private $geographicalInformation;

    public function __construct(GeographicalInformation $geographicalInformation)
    {

        $this->geographicalInformation = $geographicalInformation;
    }

    /**
     * Handle the mini grid "created" event.
     *
     * @param MiniGrid $miniGrid
     *
     * @return void
     */
    public function created(MiniGrid $miniGrid)
    {
        $this->geographicalInformation->owner_type = 'mini-grid';
        $this->geographicalInformation->owner_id = $miniGrid->id;
        if (request()->input('geo_data')) {
            $this->geographicalInformation->points = implode(',', request()->input('geo_data'));
        } else {
            $this->geographicalInformation->points = "";
        }
        $this->geographicalInformation->save();

        ClusterMetaData::query()->create([
            'cluster_id' => $miniGrid->cluster_id,
            'mini_grid_id' => $miniGrid->id,
            'energy_capacity' => 0,
            'connected_meters' => 0,
            'registered_customers' => 0
        ]);
    }

    /**
     * Handle the mini grid "updated" event.
     *
     * @param MiniGrid $miniGrid
     *
     * @return void
     */
    public function updated(MiniGrid $miniGrid)
    {
        //
    }

    /**
     * Handle the mini grid "deleted" event.
     *
     * @param MiniGrid $miniGrid
     *
     * @return void
     */
    public function deleted(MiniGrid $miniGrid)
    {
        $clusterMetaData = ClusterMetaData::query()->where('mini_grid_id', '=', $miniGrid->id);
        $clusterMetaData->delete();
    }

    /**
     * Handle the mini grid "restored" event.
     *
     * @param MiniGrid $miniGrid
     *
     * @return void
     */
    public function restored(MiniGrid $miniGrid)
    {
        //
    }

    /**
     * Handle the mini grid "force deleted" event.
     *
     * @param MiniGrid $miniGrid
     *
     * @return void
     */
    public function forceDeleted(MiniGrid $miniGrid)
    {
        //
    }
}
