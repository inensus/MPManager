<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use File;
use Storage;

class LanguagesController extends Controller
{
    private $languagesList;

    public function __constructor(LanguagesList $languagesList)
    {
        $this->languagesList = $languagesList;
    }

    public function index(): ApiResource
    {
        $fileNames = [];
        $path = resource_path('assets/locales');
        $files = \File::allFiles($path);

        foreach($files as $file) {
            array_push($fileNames, pathinfo($file)['filename']);
        }

        return new ApiResource($fileNames);
    }

}
