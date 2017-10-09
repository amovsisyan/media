<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Category;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validator\CategoriesValidation;
use App\Subcategory;
use Illuminate\Http\Request;

class SubcategoriesController extends MainCategoriesController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byName' => '2',
        'byAlias' => '3',
    ];

    protected function change()
    {
        return response()
            -> view('admin.categories.subcategories.change');
    }

    protected function createSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        $response['categories'] = Category::select('id', 'name')->get();
        $response['colLength'] = DBColumnLengthData::SUBCATEGORIES_TABLE;

        return response()
            -> view('admin.categories.subcategories.create', ['response' => $response]);
    }

    protected function createSubcategory_post(Request$request)
    {
        $validationResult = CategoriesValidation::validateSubcategoryCreate($request->all());
        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $category = Category::findOrFail($request->categorySelect);
            $createArr = [
                'name' => $request->subcategory_name,
                'alias' => $request->subcategory_alias
            ];
            $subcategory = $category->subcategories()->create($createArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
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
        $validationResult = CategoriesValidation::validateSubcategoryDelete($request->all());
        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }
        try {
            $ids[] = $request->subcategoryId;

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
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    public function editSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

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

        $searchResult = $subcategory->select('id', 'name', 'alias', 'categ_id')->get();
        $response = [];
        $response['categories'] = Category::select('id', 'name')->get();
        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $categ = $item->category()->select('id')->first();
                $response['subcategories'][] = [
                    'id' => $item->id,
                    'alias' => $item->alias,
                    'name' => $item->name,
                    'categ_id' => $categ->id,
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
        $validationResult = CategoriesValidation::validateEditSubcategorySearchValuesSave($request->all());
        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $subcategoryBuilder = Subcategory::where('id', $request->id);

            $subcat = $subcategoryBuilder->select('id', 'alias', 'categ_id')->first();
            $posts = $subcat->posts();

            // PART -> SUBCATEGORY DIRECTORY CHANGES
            // if there is even one post means there is directory with subcategory alias_id
            if ($posts->count() > 0 && $subcat->alias !== $request->newAlias) {
                $oldName = $subcat->alias . '_' . $subcat->id;
                $newName = $request->newAlias . '_' . $subcat->id;
                DirectoryEditor::updateAfterSubcategoryEdit($oldName, $newName);
            }

            // PART -> SUBCATEGORY Attach/Detach
            $categ = $subcat->category()->first();
            if ($categ->id !== (int)$request->newCategoryId) {
                $subcat->category()->associate($request->newCategoryId);
                $subcat->save();
            }

            // PART -> SUBCATEGORY UPDATE
            $updateArr = [
                'name' => $request->newName,
                'alias' => $request->newAlias
            ];
            $subcategoryBuilder->update($updateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected static function _subcategoryBySearchType($request) {
        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                return Subcategory::where('id', $request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                return Subcategory::where('name', 'like', "%$request->searchText%");
                break;
            default:
                return Subcategory::where('alias', 'like', "%$request->searchText%");
        }
    }
}
