<?php

namespace App\Http\Controllers\Helpers;

use File;
use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DirectoryEditor extends Controller
{
    const IMGCATPATH = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'cat';

    public static function clearAfterCategoryDelete($categoryIds)
    {
        $categories = Category::whereIn('id', $categoryIds)->get();
        try {
            $ids = [];
            foreach ($categories as $category) {
                $subcategories = $category->subcategories()->select('id')->get();
                foreach ($subcategories as $subcategory) {
                    $ids[] = $subcategory->id;
                }
            }
            self::clearAfterSubcategoryDelete($ids);
        } catch (\Exception $e) {
            return [
                'error' => true,
                'type' => 'Some Other Error',
                'response' => [$e->getMessage()]
            ];
        }

        return ['error' => false];
    }

    public static function clearAfterSubcategoryDelete($subcategoryIds)
    {
        try {
            $subcategories = Subcategory::whereIn('id', $subcategoryIds)->select('id', 'alias')->get();
            foreach ($subcategories as $subcategory) {
                $directory = public_path() . self::IMGCATPATH . DIRECTORY_SEPARATOR . $subcategory->alias . '_' . $subcategory->id;
                File::deleteDirectory($directory);
            }
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => false];
    }

    public static function updateAfterSubcategoryEdit($oldName, $newName)
    {
        try {
            $prefix = public_path() . self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $oldDir = $prefix . $oldName;
            $newDir = $prefix . $newName;
            $error = !rename($oldDir, $newDir);
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => $error];
    }
}
