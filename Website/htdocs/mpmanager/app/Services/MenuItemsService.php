<?php


namespace App\Services;

use App\Models\MenuItems;
use App\Models\SubMenuItems;

class MenuItemsService
{
    public function getMenuItems()
    {
        return MenuItems::with('SubMenuItems')->orderBy('menu_order')->get();
    }

    public function createMenuItems($menuItem, $subMenuItems)
    {

        $lastOrder = MenuItems::query()->latest()->first();
        $menuItem = MenuItems::create(
            [
            'name'=>$menuItem['name'],
            'url_slug'=>$menuItem['url_slug'],
            'md_icon'=>$menuItem['md_icon'],
            'menu_order'=>$lastOrder->menu_order+1,
            ]
        );

        foreach ($subMenuItems as $key => $value) {
            SubMenuItems::create(
                [
                'name'=>$value['name'],
                'url_slug'=>$value['url_slug'],
                'parent_id'=>$menuItem->id
                ]
            );
        }
    }
}
