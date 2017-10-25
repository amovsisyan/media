<?php

namespace App\Http\Controllers\Helpers;

use App\AdminNavbar;
use App\AdminNavbarParts;
use App\Category;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;

class Helpers extends Controller
{
    // ToDo Should here add CACHE part logic. Change , when PC will be 64 ))
    public static function prepareAdminNavbars($part)
    {
        $response = [];
        $response['leftNav'] = AdminNavbar::prepareLeftNavbar();
        $response['panel'] = AdminNavbarParts::preparePanelNavbar($part);
        return $response;
    }

    /**
     * get Navbar for Front
     * @return array
     */
    public static function getNavbar ()
    {
        $categories = Category::select('id', 'alias', 'name')->get();

        $response = [];
        foreach ($categories as $key => $category) {
            $response[$key]['category'] = [
                'alias'     => $category->alias,
                'name'      => $category->name,
            ];
            $subcategories = $category->subcategories()->get();
            foreach ($subcategories as $subcategory) {
                $response[$key]['subcategory'][] = [
                    'id'        => $subcategory->id,
                    'alias'     => $subcategory->alias,
                    'name'      => $subcategory->name,
                ];
            }
        }

        return  $response;
    }
}
