<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use App\Subcategory;
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
        try {
            $category = Category::findOrFail($request->categorySelect);
            $subcategory = $category->subcategories()->create(
                [
                    'name' => $request->subcategory_name,
                    'alias' => $request->subcategory_alias
                ]
            );
        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        if ($subcategory) {
            return response(['error' => false]);
        }
    }

    public function deleteSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        //  CATEGORY    &   SUBCATEGORY
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        foreach ($categories as $key => $category) {
            $response['categories'][$key][] = $category;
            $response['categories'][$key]['subcategory'] = [];
            $subcategories = $category->subcategories()->select('id', 'name')->get();
            foreach ($subcategories as $subcategory) {
                $response['categories'][$key]['subcategory'][] = $subcategory;
            }
        }

        return response()
            -> view('admin.categories.subcategories.delete', ['response' => $response]);
    }

    protected function deleteSubcategory_post(Request $request)
    {
        $ids = [];
        try {
            if ($request->subcategoryId) {
                $ids[] = $request->subcategoryId;
            }

            $deleteDir = DirectoryEditor::clearAfterSubcategoryDelete($ids);
            if (!$deleteDir['error']) {
                if (Subcategory::whereIn('id', $ids)->delete()) {
                    return response(
                        [
                            'error' => false,
                            'ids' => $ids
                        ]
                    );
                };
            }
        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        return response(['error' => false]);
    }
}
