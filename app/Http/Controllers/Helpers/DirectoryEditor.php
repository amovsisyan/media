<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Services\Locale\LocaleSettings;
use File;
use App\Category;
use App\Subcategory;
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
            $error = self::clearAfterSubcategoryDelete($ids);

            return ['error' => !$error];
        } catch (\Exception $e) {
            ResponseController::_catchedResponse($e);
        }

        return ['error' => true];
    }

    /**
     * Delete This Subcategory with inner Posts
     * @param $subcategoryIds
     * @return array
     */
    public static function clearAfterSubcategoryDelete($subcategoryIds)
    {
        try {
            $error = true;
            $subcategories = Subcategory::whereIn('id', $subcategoryIds)->select('alias')->get();
            foreach ($subcategories as $subcategory) {
                $pathTillSubCat = self::_getPathTillSubCatPlus($subcategory);
                if (File::isDirectory($pathTillSubCat)) {
                    $error = File::deleteDirectory($pathTillSubCat);
                }
            }

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }
    }

    /**
     * Changes Subcategory folder name to new Name after subcategory name edit
     * @param $oldName
     * @param $newName
     * @return array
     */
    public static function updateAfterSubcategoryEdit($oldName, $newName)
    {
        try {
            $prefix = self::_getPathTillCatPlus() . DIRECTORY_SEPARATOR;
            $oldDir = $prefix . $oldName;
            $newDir = $prefix . $newName;
            $error = File::move($oldDir, $newDir);

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }
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
            $oldName = $oldSubcat->alias;
            $newName = $newSubcat->alias;

            $prefix = self::_getPathTillCatPlus() . DIRECTORY_SEPARATOR;
            $oldDir = $prefix . $oldName;
            $newDir = $prefix . $newName;
            $postDir = $oldPost->alias;
            $postSourceDir = $oldDir . DIRECTORY_SEPARATOR . $postDir;
            if (!File::isDirectory($newDir)) {
                File::makeDirectory($newDir, 0777);
            }
            $postDestinationDir = $newDir . DIRECTORY_SEPARATOR . $postDir;
            $error = File::moveDirectory($postSourceDir, $postDestinationDir);

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }
    }

    public static function updateAfterPostAliasChanged($subcat, $oldAlias, $newAlias)
    {
        try {
            $prefix = self::_getPathTillSubCatPlus($subcat);
            $oldDir = $prefix . DIRECTORY_SEPARATOR . $oldAlias;
            $newDir = $prefix . DIRECTORY_SEPARATOR . $newAlias;

            $error = File::moveDirectory($oldDir, $newDir);

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }
    }

    public static function emptyPostFiles($subcat, $localeName, $newPost, $oldPostLocaled)
    {
        $error = !true;
        try {
            $path = self::_getPathFromSubTillPostsPlus($subcat, $newPost);
            $pathLocaled = $path . DIRECTORY_SEPARATOR . $localeName;
            $filePath = $pathLocaled . DIRECTORY_SEPARATOR . $oldPostLocaled->image;

            if (File::isFile($filePath)) {
                $error = File::delete($filePath);
            };

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

    }

    public static function movePostFiles($subcat, $localeName, $newPost, $oldImageName, $newImageName)
    {
        try {
            $path = self::_getPathFromSubTillPostsPlus($subcat, $newPost);
            $pathLocaled = $path . DIRECTORY_SEPARATOR . $localeName;
            $oldFilePath = $pathLocaled . DIRECTORY_SEPARATOR . $oldImageName;
            $newFilePath = $pathLocaled . DIRECTORY_SEPARATOR . $newImageName;

            $error = File::move($oldFilePath, $newFilePath);

            return ['error' => !$error];
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

    }
//    /**
//     * After Post Alias changed rename Post Alias folder and inside post Main Image
//     * @param $newSubcat
//     * @param $oldPost
//     * @param $post
//     * @return array
//     */
//    public static function updateAfterAliasEditedforPost($newSubcat, $oldPost, $post)
//    {
//        try {
//            $newSubCategName = $newSubcat->alias;
//            $oldName = $oldPost->alias;
//            $newName = $post->alias;
//
//            $subCategDir = self::_getPathTillCatPlus() . DIRECTORY_SEPARATOR . $newSubCategName;
//
//            $oldDir = $subCategDir . DIRECTORY_SEPARATOR  . $oldName;
//            $newDir = $subCategDir . DIRECTORY_SEPARATOR  . $newName;
//
//            $oldImg = $newDir . DIRECTORY_SEPARATOR . $oldPost->image;
//            $newImg = $newDir . DIRECTORY_SEPARATOR . $post->image;
//
//            $error = File::move($oldDir, $newDir) && File::move($oldImg, $newImg);
//
//            return ['error' => !$error];
//        } catch (\Exception $e) {
//            return ResponseController::_catchedResponse($e);
//        }
//    }

    /**
     *
     * @param $postPart
     * @return array
     */
    public static function postPartImageEdit($postPart)
    {
        try {
            $post = $postPart['postLocale']['post'];
            $subcat = $post['subcategory'];
            $toAddDir =  self::_getPathFromSubTillPartsPlus($subcat, $postPart) . DIRECTORY_SEPARATOR;
            $oldImgDir = $toAddDir . $postPart->body;
            if (File::isFile($oldImgDir)) {
                File::delete($oldImgDir);
            };

            return [
                'error' => false,
                'toAddDir' => self::_getPathFromSubTillPartsPlus($subcat, $postPart, false)
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'toAddDir' => ''
            ];
        }
    }

    /**
     * Remove Post part image after post part delete
     * @param $postPart
     * @return array
     */
    public static function removePostPartImage($postPart)
    {
        try {
            $post = $postPart['postLocale']['post'];
            $subcat = $post['subcategory'];

            $toAddDir =  self::_getPathFromSubTillPartsPlus($subcat, $postPart) . DIRECTORY_SEPARATOR;
            $imgDir = $toAddDir . $postPart->body;
            if (File::isFile($imgDir)) {
                File::delete($imgDir);
            };
            return [
                'error' => false
            ];
        } catch (\Exception $e) {
            return [
                'error' => true
            ];
        }
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
            $postDir =  self::_getPathFromSubTillPostsPlus($subcat, $post) . DIRECTORY_SEPARATOR;

            if (File::isDirectory($postDir)) {
                File::deleteDirectory($postDir);
            }

            return [
                'error' => false,
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
            ];
        }
    }

    /**
     * todo need to remove
     * Edit directories after post part Attachment
     * @param $oldPost
     * @param $newPost
     * @param $postPart
     * @return array
     */
//    public static function postPartAttachmentProcess($oldPost, $newPost, $postPart)
//    {
//        try {
//            $postParts = $newPost->postParts()->get();
//
//            // this needs for update, it helps not overwrite existing images (image names)
//            $bodyKey = self::_generateBodyKey($postParts);
//
//            $partNameExplod = explode('.', $postPart->body);
//            $extension = end($partNameExplod);
//            $newName = $newPost->alias . '_' . $bodyKey . '.' . $extension;
//
//            $subForOld = $oldPost->subcategory()->first();
//            $subForNew = $newPost->subcategory()->first();
//
//            $toMainDir = self::_getPathTillCatPlus() . DIRECTORY_SEPARATOR;
//            $fromAddDir = self::_getPathFromSubTillPartsPlus($subForOld, $oldPost) . DIRECTORY_SEPARATOR . $postPart->body;
//            $toAddDir = self::_getPathFromSubTillPartsPlus($subForNew, $newPost) . DIRECTORY_SEPARATOR . $newName;
//
//            $partOldFullDir = $toMainDir . $fromAddDir;
//            $partNewFullDir = $toMainDir . $toAddDir;
//            $error = File::move($partOldFullDir, $partNewFullDir);
//
//            return [
//                'error' => !$error,
//                'newName' => $newName
//            ];
//        } catch (\Exception $e) {
//            return [
//                'error' => true,
//                'newName' => ''
//            ];
//        }
//    }

    public static function deleteImageByPostPath($postPath)
    {
        try {
            $postFilesDir =  self::_getPathTillCatPlus() . DIRECTORY_SEPARATOR . $postPath;
            File::delete(File::files($postFilesDir));

            return [
                'error' => false,
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
            ];
        }


    }

    /**
     * public/img/cat
     * @return string
     */
    private static function _getPathTillCatPlus($withPublicPath = true) {
        return $withPublicPath
            ? public_path() . DIRECTORY_SEPARATOR . self::IMGCATPATH
            : '';
    }

    /**
     * public/img/cat/subCateg_id
     * @param $subcategory
     * @return string
     */
    private static function _getPathTillSubCatPlus($subcategory, $withPublicPath = true) {
        return self::_getPathTillCatPlus($withPublicPath) . DIRECTORY_SEPARATOR . $subcategory->alias;
    }

    /**
     * subCateg_id/post_id
     * @param $subcat
     * @param $post
     * @return string
     */
    private static function _getPathFromSubTillPostsPlus($subcat, $post, $withPublicPath = true) {
        return self::_getPathTillSubCatPlus($subcat, $withPublicPath) .  DIRECTORY_SEPARATOR . $post->alias;
    }

    /**
     * subCateg_id/post_id/parts
     * @param $subcat
     * @param $post
     * @return string
     */
    private static function _getPathFromSubTillPartsPlus($subcat, $postPart, $withPublicPath = true) {
        $postLocale = $postPart['postLocale'];
        $post = $postLocale['post'];
        return self::_getPathFromSubTillPostsPlus($subcat, $post, $withPublicPath)
            . DIRECTORY_SEPARATOR . LocaleSettings::getLocaleNameById($postLocale->locale_id)
            . DIRECTORY_SEPARATOR . self::PARTS;
    }

    /**
     * We need to get some not 'busy' counter to not overwrite existing image
     * cause part images are POST_ALIAS + SOME_COUNTER
     * @param $postParts
     * @return mixed
     */
    private static function _generateBodyKey($postParts) {
        $arrOfBusyNums = [];
        if ($postParts->count()) {
            foreach ($postParts as $part) {
                $explodedOnce =  explode('_', $part->body);
                $lastPart = end($explodedOnce);
                $arrOfBusyNums[] = explode('.', $lastPart)[0];
            }
        };
        return max($arrOfBusyNums) + 1;
    }
}
