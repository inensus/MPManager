<?php

namespace App;

use App\Models\SubMenuItems;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItems extends Model
{
    public function subMenuItems(): HasMany
    {
        return $this->hasMany(SubMenuItems::class, 'parent_id');
    }
}
