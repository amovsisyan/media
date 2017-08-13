<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Helpers;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;

class SubcategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.subcategories.change');
    }

    protected function createSubcategory_get()
    {
        $response = $this->prepareNavbars(request()->segment(3));
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        $response['categories'] = Category::select('id', 'name')->get();

        return response()
            -> view('admin.categories.subcategories.create', ['response' => $response]);
    }

    protected function createSubcategory_post(Request$request)
    {
        $validator = Validator::make($request->all(), [
            'subcategory_name' => 'required|min:2|max:30',
            'subcategory_alias' => 'required|min:2|max:30',
            'categorySelect' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = [];
            foreach ($errors->all() as $message) {
                $response[] = $message;
            }
            return response(
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $response
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
