<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use File;
use Storage;

/**
 * @group   Language
 * Class LanguageController
 * @package App\Http\Controllers
 */

class LanguageController extends Controller
{
    private $languagesList;

    public function __constructor(LanguagesList $languagesList)
    {
        $this->languagesList = $languagesList;
    }

    /**
     * List of all supported languages
     * @return ApiResource
     */
    public function index(): ApiResource
    {
        $path = resource_path('assets/locales');
        $files = collect(\File::allFiles($path));
        $filteredFiles = $files->map(
            function ($file) {
                if ($file->getExtension() === 'json') {
                    return $file->getFilename();
                }
            }
        );
        return new ApiResource($filteredFiles);
    }
}
