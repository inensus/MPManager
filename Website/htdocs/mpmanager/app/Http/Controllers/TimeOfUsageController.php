<?php

namespace App\Http\Controllers;

use App\Models\TimeOfUsage;
use Illuminate\Http\Request;

class TimeOfUsageController extends Controller
{
    public function destroy(TimeOfUsage $timeOfUsage)
    {

        return TimeOfUsage::find($timeOfUsage->id)->delete();
    }
}
