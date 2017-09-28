<?php

namespace App\Http\Controllers\Helpers;

use File;
use App\Category;
use App\Subcategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DirectoryEditor extends Controller
{
    const IMGCATPATH = 'img' . DIRECTORY_SEPARATOR . 'cat';
    const PARTS = 'parts';

    /**
     * Delete All Category's Subcategory folders with Posts
     * @param $categoryIds
     * @return array
     */
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

    /**
     * Delete This Subcategory with inner Posts
     * @param $subcategoryIds
     * @return array
     */
    public static function clearAfterSubcategoryDelete($subcategoryIds)
    {
        try {
            $subcategories = Subcategory::whereIn('id', $subcategoryIds)->select('id', 'alias')->get();
            foreach ($subcategories as $subcategory) {
                $directory = public_path() . DIRECTORY_SEPARATOR . self::IMGCATPATH .
                    DIRECTORY_SEPARATOR . $subcategory->alias . '_' . $subcategory->id;
                File::deleteDirectory($directory);
            }
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => false];
    }

    /**
     * Changes SUbcategory folder name to new Name after subcategory name edit
     * @param $oldName
     * @param $newName
     * @return array
     */
    public static function updateAfterSubcategoryEdit($oldName, $newName)
    {
        try {
            $prefix = public_path() . DIRECTORY_SEPARATOR . self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $oldDir = $prefix . $oldName;
            $newDir = $prefix . $newName;
            // todo use File Facade
            $error = !rename($oldDir, $newDir);
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => $error];
    }

    /** Moved all from OldName Subcategory folder into NewName Subcategory Folder
     * @param $oldSubcat
     * @param $newSubcat
     * @param $oldPost
     * @return array
     */
    public static function updateAfterSubcategoryEditforPost($oldSubcat, $newSubcat, $oldPost)
    {
        try {
            $oldName = $oldSubcat->alias . '_' . $oldSubcat->id;
            $newName = $newSubcat->alias . '_' . $newSubcat->id;

            $prefix = public_path() . DIRECTORY_SEPARATOR . self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $oldDir = $prefix . $oldName;
            $newDir = $prefix . $newName;
            $postDir = $oldPost->alias . '_' . $oldPost->id;
            $postSourceDir = $oldDir . DIRECTORY_SEPARATOR . $postDir;
            $postDestinationDir = $newDir . DIRECTORY_SEPARATOR . $postDir;
            $error = !File::moveDirectory($postSourceDir, $postDestinationDir);
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => $error];
    }

    /**
     * After Post Alias changed rename Post Alias folder and inside post Main Image
     * @param $newSubcat
     * @param $oldPost
     * @param $post
     * @return array
     */
    public static function updateAfterAliasEditedforPost($newSubcat, $oldPost, $post)
    {
        try {
            $newSubCategName = $newSubcat->alias . '_' . $newSubcat->id;
            $oldName = $oldPost->alias . '_' . $oldPost->id;
            $newName = $post->alias . '_' . $post->id;

            $subCategDir = public_path() . DIRECTORY_SEPARATOR . self::IMGCATPATH . DIRECTORY_SEPARATOR . $newSubCategName;

            $oldDir = $subCategDir . DIRECTORY_SEPARATOR  . $oldName;
            $newDir = $subCategDir . DIRECTORY_SEPARATOR  . $newName;

            $oldImg = $newDir . DIRECTORY_SEPARATOR . $oldPost->image;
            $newImg = $newDir . DIRECTORY_SEPARATOR . $post->image;

            $error = !File::move($oldDir, $newDir) || !File::move($oldImg, $newImg);
        } catch (\Exception $e) {
            return ['error' => true];
        }

        return ['error' => $error];
    }

    /**
     *
     * @param $postPart
     * @param $oldPostPart
     * @return array
     */
    public static function postPartImageEdit($postPart, $oldPostPart)
    {
        try {
            $post = $postPart->post()->first();
            $subcat = $post->subcategory()->first();
            $toMainDir = self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $toAddDir =  $subcat->alias . '_' . $subcat->id .  DIRECTORY_SEPARATOR .
                $post->alias . '_' . $post->id . DIRECTORY_SEPARATOR .
                DirectoryEditor::PARTS . DIRECTORY_SEPARATOR;
            $oldImgDir = $toMainDir . $toAddDir . $oldPostPart->body;
            if (File::isFile($oldImgDir)) {
                File::delete($oldImgDir);
            };
            $error = false;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'toAddDir' => ''
            ];
        }

        return [
            'error' => $error,
            'toAddDir' => $toAddDir
        ];
    }

    /**
     * Remove Post part image after post part delete
     * @param $postPart
     * @return array
     */
    public static function removePostPartImage($postPart)
    {
        try {
            $post = $postPart->post()->first();
            $subcat = $post->subcategory()->first();
            $toMainDir = self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $toAddDir =  $subcat->alias . '_' . $subcat->id .  DIRECTORY_SEPARATOR .
                $post->alias . '_' . $post->id . DIRECTORY_SEPARATOR .
                DirectoryEditor::PARTS . DIRECTORY_SEPARATOR;
            $imgDir = $toMainDir . $toAddDir . $postPart->body;
            if (File::isFile($imgDir)) {
                File::delete($imgDir);
            };
            $error = false;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'toAddDir' => ''
            ];
        }

        return [
            'error' => $error,
            'toAddDir' => $toAddDir
        ];
    }

    /**
     * Remove Post dir after post delete
     * @param $post
     * @return array
     */
    public static function removePostDir($post)
    {
        try {
            $subcat = $post->subcategory()->first();
            $toMainDir = self::IMGCATPATH . DIRECTORY_SEPARATOR;
            $toAddDir =  $subcat->alias . '_' . $subcat->id .  DIRECTORY_SEPARATOR .
                $post->alias . '_' . $post->id . DIRECTORY_SEPARATOR;
            $postDir = $toMainDir . $toAddDir;
            if (File::isDirectory($postDir)) {
                File::deleteDirectory($postDir);
            }
            $error = false;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'toAddDir' => ''
            ];
        }

        return [
            'error' => $error,
            'toAddDir' => $toAddDir
        ];
    }
}
