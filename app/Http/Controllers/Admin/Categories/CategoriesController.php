<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\CategoryLocale;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Http\Controllers\Services\Locale\LocaleSettings;

use App\Http\Requests\AdminSide\CategoryRequest\CreateCategoryRequest;
use App\Http\Requests\AdminSide\CategoryRequest\DeleteCategoryRequest;
use App\Http\Requests\AdminSide\CategoryRequest\EditCategorySaveRequest;
use App\Http\Requests\AdminSide\CategoryRequest\EditCategorySearchRequest;

class CategoriesController extends MainCategoriesController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byAlias' => '2'
    ];

    protected function change()
    {
        return response()
            -> view('admin.categories.categories.change');
    }

    protected function createCategory_get()
    {
        $response = Helpers::prepareAdminNavbars();

        $response['colLength'] = DBColumnLengthData::getCategoryLenghts();
        $response['activeLocales'] = LocaleSettings::getActiveLocales();

        return response()
            -> view('admin.categories.categories.create', ['response' => $response]);
    }

    protected function createCategory_post(CreateCategoryRequest $request)
    {
        $allRequest = $request->all();
        try {
            $alias = Helpers::removeSpaces($request->category_alias);
            $category = Category::create(['alias' => $alias]);

            $createArr = [];
            foreach ($allRequest['categories_names'] as $cat) {
                $createArr[] = [
                    'name' => $cat['name'],
                    'locale_id' => $cat['locale_id']
                ];
            }

            $categoryLocale = $category->categoriesLocale()
                ->createMany($createArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected function deleteCategory_get()
    {
        $response = Helpers::prepareAdminNavbars();

        $categories = Category::select('id', 'alias')->get();

        $response['categories'] = ResponsePrepareHelper::PR_DeleteCategory($categories);

        return response()
            -> view('admin.categories.categories.delete', ['response' => $response]);
    }

    protected function deleteCategory_post(DeleteCategoryRequest $request)
    {
        $requestData = $request->data;
        try {
            $ids = [];
            if (!empty($requestData)) {
                foreach ($requestData as $id) {
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
        $response = Helpers::prepareAdminNavbars();

        return response()
            -> view('admin.categories.categories.edit', ['response' => $response]);
    }

    protected function editCategory_post(EditCategorySearchRequest $request)
    {
        try {
            $category = self::_categoryBySearchType($request);

            $searchResult = $category->select('id', 'alias')
                ->with(['categoriesLocale' => function ($query) {
                    $query->select('id', 'name', 'categ_id');
                }])
                ->get();
            $response = [];

            if (!empty($searchResult)) {
                foreach ($searchResult as $category) {
                    $categoriesLocale = [];
                    foreach ($category['categoriesLocale'] as $categoryLocale) {
                        $categoriesLocale[] = [
                            'id' => $categoryLocale->id,
                            'name' => $categoryLocale->name,
                            'localeAbbr' => LocaleSettings::getLocaleNameById($categoryLocale->id)
                        ];
                    }
                    $response[] = [
                        'id' => $category->id,
                        'alias' => $category->alias,
                        'categoriesLocale' => $categoriesLocale
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

    protected function editCategorySave_post(EditCategorySaveRequest $request)
    {
        try {
            $allRequest = $request->all();

            // Category Main update
            $updateArr = [
                'alias' => $allRequest['catAlias']
            ];
            Category::updCategoryByID($allRequest['catId'], $updateArr);

            // Category Locale update
            foreach ($allRequest['localesInfo'] as $locale) {
                $localeUpdateArr = [
                    'name' => $locale['name']
                ];
                CategoryLocale::updLocaleCategoryByID($locale['locale_id'], $localeUpdateArr);
            }
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
            case self::CATEGORYEDITSEARCHTYPES['byAlias']:
                return Category::getCategoriesBuilderLikeAlias($request->searchText);
                break;
            default:
                throw new \Exception('Can not find. Wrong category type');
        }
    }
}
