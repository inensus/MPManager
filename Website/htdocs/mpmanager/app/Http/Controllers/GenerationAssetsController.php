<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Services\GenerationAssetsService;

/**
 * @group   Generation Assets
 * Class GenerationAssetsController
 * @package App\Http\Controllers
 */

class GenerationAssetsController extends Controller
{
    /**
     * @var GenerationAssetsService
     */
    private $generationAssetsService;

    /**
     * GenerationAssetsController constructor.
     *
     * @param GenerationAssetsService $generationAssetsService
     */
    public function __construct(GenerationAssetsService $generationAssetsService)
    {
        $this->generationAssetsService = $generationAssetsService;
    }

    /**
     * Generation Assets
     * Generation assets of specified miniGrid for between given dates.
     * @urlParam miniGridId required.
     * @responseFile responses/generationAssets/generationAssets.json
     * @param $miniGridId
     * @return ApiResource
     */
    public function show($miniGridId): ApiResource
    {
        return new ApiResource(array_values($this->generationAssetsService->getGenerationAssets($miniGridId)));
    }
}
