<?php

namespace App\Services;

use App\Models\Plugins;

class PluginsService
{
    public function getPlugins()
    {
        return Plugins::query()->orderBy('id')->get();
    }

    public function addPlugin($name,$composer_name, $description)
    {

        Plugins::query()->create([
            'name' => $name,
            'composer_name' => $composer_name,
            'description' => $description,
        ]);
    }




}
