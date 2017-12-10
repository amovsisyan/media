<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validator\CategoriesValidation;
use App\Http\Controllers\Services\Locale\LocaleSettings;
use App\Subcategory;
use App\SubcategoryLocale;
use Illuminate\Http\Request;

class SubcategoriesController extends MainCategoriesController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byAlias' => '2',
    ];

    protected function change()
    {
        return response()
            -> view('admin.categories.subcategories.change');
    }

    protected function createSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars();
        $response['categories'] = Category::select('id', 'alias')->get();
        $response['colLength'] = DBColumnLengthData::getSubCategoryLenghts();
        $response['activeLocales'] = LocaleSettings::getActiveLocales();

        return response()
            -> view('admin.categories.subcategories.create', ['response' => $response]);
    }

    protected function createSubcategory_post(Request$request)
    {
        $allRequest = $request->all();
        $allRequest['subcategoryNames'] = Helpers::jsonObjList2arrayList($allRequest['subcategoryNames']);

        $validationResult = CategoriesValidation::validateSubcategoryCreate($allRequest);

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $createArr = [
                'alias' =>$allRequest['subcategoryAlias']
            ];
            $subCategory = Category::createSubCategoryByID($allRequest['categoryId'], $createArr);

            $localeCreateArr = [];
            foreach ($allRequest['subcategoryNames'] as $cat) {
                $localeCreateArr[] = [
                    'name' => $cat['name'],
                    'locale_id' => $cat['locale_id']
                ];
            }

            $categoryLocale = $subCategory->subcategoriesLocale()
                ->createMany($localeCreateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    public function deleteSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars();

        //  CATEGORY    &   SUBCATEGORY
        $categories = Category::getCategoriesWithSubcategories();

        foreach ($categories as $key => $category) {
            $response['categories'][$key][] = $category;
            $response['categories'][$key]['subcategory'] = [];
            $subcategories = $category['subcategories'];
            foreach ($subcategories as $subcategory) {
                $response['categories'][$key]['subcategory'][] = $subcategory;
            }
        }

        return response()
            -> view('admin.categories.subcategories.delete', ['response' => $response]);
    }

    protected function deleteSubcategory_post(Request $request)
    {
        $validationResult = CategoriesValidation::validateSubcategoryDelete($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }
        try {
            $ids[] = $request->subcategoryId;

            // todo part
//             $deleteDir = DirectoryEditor::clearAfterSubcategoryDelete($ids);
//            if (!$deleteDir['error']) {
//                if (Subcategory::delSubCategoriesByIDs($ids)) {
//                    return response(
//                        [
//                            'error' => false,
//                            'ids' => $ids
//                        ]
//                    );
//                };
//            }
            if (Subcategory::delSubCategoriesByIDs($ids)) {
                return response(
                    [
                        'error' => false,
                        'ids' => $ids
                    ]
                );
            };
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    public function editSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars();

        return response()
            -> view('admin.categories.subcategories.edit', ['response' => $response]);
    }

    public function editSubcategory_post(Request $request)
    {
        $validationResult = CategoriesValidation::validateEditSubcategorySearchValues($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        $subcategory = self::_subcategoryBySearchType($request);

        $searchResult = $subcategory->select('id', 'alias', 'categ_id')
            ->with('subcategoriesLocale', 'category')
            ->get();

        $response = [];
        $response['subcategories'] = [];
        $categories = Category::select('id', 'alias')->get();
        foreach ($categories as $category) {
            $response['categories'][] = [
                'id' => $category->id,
                'alias' => $category->alias
            ];
        }

        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $categ = $item['category'];
                $subcategoryLocale = $item['subcategoriesLocale'];
                $subcategoriesLocale = [];

                foreach ($subcategoryLocale as $localedSub) {
                    $subcategoriesLocale[] = [
                        'id' => $localedSub->id,
                        'name' => $localedSub->name,
                        'locale_id' => $localedSub->locale_id,
                        'locale_name' => LocaleSettings::getLocaleNameById($localedSub->locale_id)
                    ];
                }

                $response['subcategories'][] = [
                    'id' => $item->id,
                    'alias' => $item->alias,
                    'categ_id' => $categ->id,
                    'subcategoriesLocale' => $subcategoriesLocale,
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

    protected function editSubcategorySave_post(Request $request)
    {
        $requestAll =  $request->all();
        $requestAll['subcategoryNames'] = json_decode($requestAll['subcategoryNames'], true);
        $validationResult = CategoriesValidation::validateEditSubcategorySearchValuesSave($requestAll);

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $subcategoryBuilder = Subcategory::getSubCategoryBuilderByID($requestAll['id']);

            $subcat = $subcategoryBuilder
                ->with([
                    'posts',
                    'category',
                    'subcategoriesLocale'
                ])->select('id', 'alias', 'categ_id')
                ->first();

            // SUBCATEGORY UPDATE & DIRECTORY CHANGES
            // if there is even one post means there is directory with subcategory alias_id
            $posts = $subcat['posts'];
            if ($posts->count() > 0 && $subcat->alias !== $requestAll['newAlias']) {
                $oldName = $subcat->alias;
                $newName = $requestAll['newAlias'];
                DirectoryEditor::updateAfterSubcategoryEdit($oldName, $newName);

                $updateArr = [
                    'alias' => $request->newAlias
                ];
                $subcategoryBuilder->update($updateArr);
            }

            // SUBCATEGORY Attach/Detach
            $categ = $subcat['category'];
            if ($categ->id !== (int)$requestAll['newCategoryId']) {
                $subcat->category()->associate($requestAll['newCategoryId']);
                $subcat->save();
            }

            // Subcategory Locale Name changes
            foreach ($requestAll['subcategoryNames'] as $subcategoryLocale) {
                $id = $subcategoryLocale['id'];
                $name = $subcategoryLocale['name'];

                $subLocaleModel = SubcategoryLocale::findOrFail($id);
                $subLocaleModel->name = $name;
                $subLocaleModel->save();
            }
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected static function _subcategoryBySearchType($request) {
        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                return Subcategory::getSubCategoryBuilderByID($request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byAlias']:
                return Subcategory::getSubCategoriesBuilderLikeAlias($request->searchText);
                break;
            default:
                throw new \Exception('Can not find. Wrong subCategory type');
        }
    }
}
