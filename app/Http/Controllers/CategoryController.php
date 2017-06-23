<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    protected function category (Request $request)
    {
        $response['navbar'] = $this->getNavbar();

        return response()
            -> view('welcome', ['response' => $response]);
    }

    public function getNavbar ()
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
