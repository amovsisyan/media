<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use Illuminate\Http\Request;

class SubcategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.subcategories.change');
    }

    protected function createSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        $response['categories'] = Category::select('id', 'name')->get();

        return response()
            -> view('admin.categories.subcategories.create', ['response' => $response]);
    }

    protected function createSubcategory_post(Request$request)
    {
        $validationResult = Validation::validateSubcategoryCreate($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        $category = Category::findOrFail($request->categorySelect);
        $subcategory = $category->subcategories()->create(
            [
                'name' => $request->subcategory_name,
                'alias' => $request->subcategory_alias
            ]
        );

        if ($subcategory) {
            return response(['error' => false]);
        }
    }
}
