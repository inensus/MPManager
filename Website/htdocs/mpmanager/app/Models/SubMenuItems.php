<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubMenuItems extends Model
{
    public function menuItems(): BelongsTo
    {
        return $this->belongsTo(MenuItems::class);
    }
}
