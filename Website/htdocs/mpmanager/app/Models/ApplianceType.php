<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Inensus\Ticket\Models\Ticket;

class ApplianceType extends Model
{
    protected $fillable = ['name'];
}
