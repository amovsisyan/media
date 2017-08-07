<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\AdminController;
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
                ['error' => true,
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

    // Prepare Left and Panel Navbar response
    // Must be like helper as for now it also the same logic used in SubCategories
    // think will be used everywhere,
    // ToDo Should here add CACHE part logic
    protected function prepareNavbars($part)
    {
        $response = [];
        $adminController = new AdminController();
        $response['leftNav'] = $adminController->getLeftNavbar();
        $response['panel'] = $adminController->getPanelNavbar($part);
        return $response;
    }
}
