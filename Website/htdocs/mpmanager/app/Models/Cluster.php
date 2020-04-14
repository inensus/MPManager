<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Cluster
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property int manager_id
 * @property string $updated_at
 * @property string $created_at
 */
class Cluster extends Model
{
    /**
     * A cluster has a cluster-manager who is responsible for the cluster.
     *
     * @return BelongsTo
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function miniGrids(): HasMany
    {
        return $this->hasMany(MiniGrid::class);
    }
}
