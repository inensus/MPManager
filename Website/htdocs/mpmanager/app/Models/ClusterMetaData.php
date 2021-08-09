<?php

namespace App\Models;

/**
 * Class ClusterMetaData
 *
 * @package App
 *
 * @property int $id
 * @property int $cluster_id
 * @property int $mini_grid_id
 * @property int $energy_capacity
 * @property int $connected_meters
 * @property int $registered_customers
 */

class ClusterMetaData extends BaseModel
{
    protected $guarded = [];
}
