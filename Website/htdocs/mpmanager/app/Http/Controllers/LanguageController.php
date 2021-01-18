<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use File;
use Storage;


class LanguageController extends Controller
{
    private $languagesList;

    public function __constructor(LanguagesList $languagesList)
    {
        $this->languagesList = $languagesList;
    }

    public function index(): ApiResource
    {

        $path = resource_path('assets/locales');
        $files = \File::allFiles($path);
        $collection = collect($files);

        $filteredFiles = $collection->map(function ($value){
            if($value->getExtension() === 'json'){
                return $value->getFilename();
            }

        });

        return new ApiResource($filteredFiles);
    }

}
