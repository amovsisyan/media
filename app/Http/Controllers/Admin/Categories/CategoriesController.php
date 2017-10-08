<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validator\CategoriesValidation;
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
        $response['colLength'] = DBColumnLengthData::CATEGORIES_TABLE;

        return response()
            -> view('admin.categories.categories.create', ['response' => $response]);
    }

    protected function createCategory_post(Request $request)
    {
        $validationResult = CategoriesValidation::validateCategoryCreate($request->all());
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
            $category = Category::create(
                [
                    'name' => $request->category_name,
                    'alias' => $request->category_alias
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

        if ($category) {
            return response(['error' => false]);
        }
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
                foreach (json_decode($request->data) as $id) {
                    $ids[] = $id;
                }
            }

            $deleteDir = DirectoryEditor::clearAfterCategoryDelete($ids);
            if (!$deleteDir['error']) {
                if (Category::whereIn('id', $ids)->delete()) {
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

    protected function editCategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.categories.categories.edit', ['response' => $response]);
    }

    protected function editCategory_post(Request $request)
    {
        $validationResult = CategoriesValidation::validateEditCategorySearchValues($request->all());
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
        $validationResult = CategoriesValidation::validateEditCategorySearchValuesSave($request->all());
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
