<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\ElasticUsageTime;
use Illuminate\Http\Request;

class ElasticUsageTimeController extends Controller
{

    /*
     * @param ElasticUsageTime $elasticUsage
     * @return ApiResource
     * @throws \Exception
     */
    public function destroy(ElasticUsageTime $elasticUsage)
    {
        return ElasticUsageTime::find($elasticUsage->id)->delete();
    }
}
