<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 13.07.18
 * Time: 13:46
 */

namespace App\Models\Role;


use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Roles extends BaseModel
{

    /**
     * @return belongsTo
     */
    public function definitions(): belongsTo
    {
        return $this->belongsTo(RoleDefinition::class, 'role_definition_id');
    }

    public function roleOwner()
    {
        return $this->morphTo();
    }
}
