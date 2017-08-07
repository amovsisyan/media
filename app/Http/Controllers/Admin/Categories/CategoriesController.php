<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;

class CategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.categories.change');
    }

    protected function createCategory_get()
    {
        $response = $this->prepareNavbars(request()->segment(3));

        return response()
            -> view('admin.categories.categories.create', ['response' => $response]);
    }

    protected function createCategory_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|min:2|max:30',
            'category_alias' => 'required|min:2|max:30',
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

        $category = Category::create(
            [
                'name' => $request->category_name,
                'alias' => $request->category_alias
            ]
        );

        if ($category) {
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
