<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected function index (Request $request)
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
                    'alias'     => $subcategory->alias,
                    'name'      => $subcategory->name,
                ];
            }
        }

        return response()
            -> view('welcome', ['response' => $response]);
    }
}
