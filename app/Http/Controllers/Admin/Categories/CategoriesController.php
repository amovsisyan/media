<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use Illuminate\Http\Request;
use Mockery\Exception;

class CategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.categories.change');
    }

    protected function createCategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.categories.categories.create', ['response' => $response]);
    }

    protected function createCategory_post(Request $request)
    {
        $validationResult = Validation::validateCategoryCreate($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
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

        return response(
            [
                'error' => true,
            ], 404
        );
    }

    protected function deleteCategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        $response['categories'] = Category::select('id', 'name')->get();

        return response()
            -> view('admin.categories.categories.delete', ['response' => $response]);
    }

    protected function deleteCategory_post(Request $request)
    {
        try {
            if ($request->data) {
                $ids = [];
                foreach (json_decode($request->data) as $category) {
                    $exp_cat = explode('_', $category);
                    $ids[] = $exp_cat[count($exp_cat)-1];
                }
            }
            if (Category::whereIn('id', $ids)->delete()) {
                return response(
                    [
                        'error' => false,
                        'ids' => $ids
                    ]
                );
            }
        } catch(Exception $e) {
            return response(
                [
                    'error' => true,
                    'response' => $e->getMessage()
                ], 404
            );
        }
    }
}
