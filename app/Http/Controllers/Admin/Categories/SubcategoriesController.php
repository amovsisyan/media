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

    public function editSubcategory_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.categories.subcategories.edit', ['response' => $response]);
    }

    public function editSubcategory_post(Request $request)
    {
        $validationResult = Validation::validateEditSubcategorySearchValues($request->all());
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
                $subcategory = Subcategory::where('id', $request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                $subcategory = Subcategory::where('name', 'like', "%$request->searchText%");
                break;
            default:
                $subcategory = Subcategory::where('alias', 'like', "%$request->searchText%");
        }

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
        $validationResult = Validation::validateEditSubcategorySearchValuesSave($request->all());
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
            $subcategoryBuilder = Subcategory::where('id', $request->id);

            $subcat = $subcategoryBuilder->select('id', 'alias', 'categ_id')->first();
            $posts = $subcat->posts();

            // PART -> SUBCATEGORY DIRECTORY CHANGES
            // if there is even one post means there is directory with subcategory alias_id
            if ($posts->count() > 0 && $subcat->alias !== $request->newAlias) {
                $oldName = $subcat->alias . '_' . $subcat->id;
                $newName = $request->newAlias . '_' . $subcat->id;
                $result = DirectoryEditor::updateAfterSubcategoryEdit($oldName, $newName);
                if ($result['error']) {
                    throw new \Exception("Directory rename Error");
                }
            }

            // PART -> SUBCATEGORY Attach/Detach
            $categ = $subcat->category()->first();
            if ($categ->id !== (int)$request->newCategoryId) {
                $subcat->category()->associate($request->newCategoryId);
                $subcat->save();
            }

            // PART -> SUBCATEGORY UPDATE
            $subcategoryBuilder->update([
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
