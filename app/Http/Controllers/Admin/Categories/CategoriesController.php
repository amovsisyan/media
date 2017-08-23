<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use Illuminate\Http\Request;

class CategoriesController extends MainCategoriesController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byName' => '2',
        'byAlias' => '3',
    ];

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
        $ids = [];
        try {
            if ($request->data) {
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

    protected function editCategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.categories.categories.edit', ['response' => $response]);
    }

    protected function editCategory_post(Request $request)
    {
        $validationResult = Validation::validateEditCategorySearchValues($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                $category = Category::where('id', $request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                $category = Category::where('name', 'like', "%$request->searchText%");
                break;
            default:
                $category = Category::where('alias', 'like', "%$request->searchText%");
        }
        $searchResult = $category->select('id', 'name', 'alias')->get();
        $response = [];
        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $response[] = [
                    'id' => $item->id,
                    'alias' => $item->alias,
                    'name' => $item->name
                ];
            }
        }
        return response(
            [
                'error' => false,
                'response' => $response
            ]
        );
    }

    protected function editCategorySave_post(Request $request)
    {
        $validationResult = Validation::validateEditCategorySearchValuesSave($request->all());
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
            Category::where('id', $request->id)
                ->update([
                    'name' => $request->newName,
                    'alias' => $request->newAlias
                ]);
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
