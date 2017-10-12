<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Admin\Response\ResponseController;
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
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $createArr = [
                'name' => $request->category_name,
                'alias' => $request->category_alias
            ];
            Category::create($createArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
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
        // couldn't validate category IDs cause response is json
        $ids = [];
        try {
            if ($request->data) {
                foreach (json_decode($request->data) as $id) {
                    $ids[] = $id;
                }
            }

            $deleteDir = DirectoryEditor::clearAfterCategoryDelete($ids);

            if (!$deleteDir['error']) {
                if (Category::delCategoriesByIDs($ids)) {
                    return response(
                        [
                            'error' => false,
                            'ids' => $ids
                        ]
                    );
                };
            }
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
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
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $category = self::_categoryBySearchType($request);

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
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }
    }

    protected function editCategorySave_post(Request $request)
    {
        $validationResult = CategoriesValidation::validateEditCategorySearchValuesSave($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $updateArr = [
                'name' => $request->newName,
                'alias' => $request->newAlias
            ];
            Category::updCategoryByID($request->id, $updateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected static function _categoryBySearchType($request) {
        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                return Category::getCategoryBuilderByID($request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                return Category::getCategoriesBuilderLikeName($request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byAlias']:
                return Category::getCategoriesBuilderLikeAlias($request->searchText);
                break;
            default:
                throw new \Exception('Can not find. Wrong category type');
        }
    }
}
