<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Http\Controllers\Helpers\DirectoryEditor;
use App\Http\Controllers\Services\Locale\LocaleSettings;
use App\Post;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validator\PostsValidation;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\PostParts;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Data\DBColumnLengthData;
use File;

class CrudController extends PostsController
{
    const POSTEDITSEARCHTYPES = [
        'byID' => '1',
        'byAlias' => '2',
    ];

    const TEMPLATE_MAX_COLUMNS = 12;

    /**
     * Returns Data for Post Creation View
     * @return \Illuminate\Http\Response
     */
    protected function createPost_get()
    {
        $response = Helpers::prepareAdminNavbars();

        //  All CATEGORY    &   SUBCATEGORY
        $getAllCatSubcat = self::_getAllCategoriesSubcategories();
        $response['categories'] = !empty($getAllCatSubcat) && !empty($getAllCatSubcat['categories']) ? $getAllCatSubcat['categories'] : [];

        //  All HASHTAGS
        $getAllHashtags = self::_getAllHashtags();
        $response['hashtags'] = !empty($getAllHashtags['hashtags']) ? $getAllHashtags['hashtags'] : [];

        $response['colLength'] = [
            'post' => DBColumnLengthData::getPostLenghts(),
            'parts' => DBColumnLengthData::getPostPartsLenght(),
        ];

        $response['activeLocales'] = LocaleSettings::getActiveLocales();
        $response['templateDivider'] = self::TEMPLATE_MAX_COLUMNS / count($response['activeLocales']);

        return response()
            -> view('admin.posts.crud.create', ['response' => $response]);
    }

    /**
     * Create New Post with Post Parts
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function createPost_post(Request $request)
    {
        $requestAll = $request->all();
        $requestAll['postHashtag'] = json_decode($requestAll['postHashtag']);
        $requestAll['activeLocales'] = json_decode($requestAll['activeLocales']);

        // Main fields Validation
        $mainValidation = PostsValidation::createPostMainFieldsValidations($requestAll);

        if ($mainValidation['error']) {
            return ResponseController::_validationResultResponse($mainValidation);
        }

        // Part fields Validation
        $partValidation = PostsValidation::createPostPartFieldsValidations($requestAll);

        if ($partValidation['error']) {
            return ResponseController::_validationResultResponse($partValidation);
        }

        try {
            // Post Main Creation
            $createMainRes = self::_createPostMain($request);
            $post = $createMainRes['post'];
            $postsLocale = $createMainRes['postsLocale'];
            $mainPath = $createMainRes['mainPath'];

            // Post Parts Creation
            self::_createPostParts($request, $postsLocale, $mainPath);

            // Hashtag Attach
            $post->hashtags()->attach(json_decode($request->postHashtag));

        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Add New Post Parts
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postAddNewPart_post(Request $request, $locale, $id)
    {
        $requestAll = $request->all();

        // Part fields Validation
        $partValidation = PostsValidation::createPostPartFieldsValidations($requestAll);

        if ($partValidation['error']) {
            return ResponseController::_validationResultResponse($partValidation);
        }

        try {
            $post = Post::postWithSubcategoryPostLocaleById($id, $requestAll['locale']);
            $postLocale = $post['postLocale'];
            $subcategory = $post['subcategory'];
            $mainPath = $subcategory->alias . DIRECTORY_SEPARATOR . $post->alias;

            // Post Parts Creation
            self::_createPostParts($request, $postLocale, $mainPath);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    // todo rename this method and add annotation
    protected function updatePost_get()
    {
        $response = Helpers::prepareAdminNavbars();

        return response()
            -> view('admin.posts.crud.update', ['response' => $response]);
    }

    // todo rename this method and add annotation
    // IMPORTANT
    // This method calls in two places, see web.php
    protected function updatePost_post(Request $request)
    {
        $validationResult = PostsValidation::validateEditPostSearchValues($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        $post = self::_postBySearchType($request);

        $searchResult = $post->select('id', 'alias')->get();
        $response['posts'] = [];
        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $response['posts'][] = [
                    'id' => $item->id,
                    'alias' => $item->alias
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

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function postMainDetails_get(Request $request, $alias, $id)
    {
        $response = Helpers::prepareAdminNavbars();

        try {
            $post = Post::postWithPostLocaleHashtagsById($id);

            $response['post'] = [
                "id" => $post->id,
                "alias" => $post->alias,
                "subcateg_id" => $post->subcateg_id
            ];

            $postsLocale = $post['postLocale'];
            $response['post']['postLocale'] = [];
            foreach ($postsLocale as $postLocale) {
                $response['post']['postLocale'][] = [
                    "header" => $postLocale->header,
                    "text" => $postLocale->text,
                    "image" => $postLocale->image,
                    "localeName" => LocaleSettings::getLocaleNameById($postLocale->locale_id),
                    "localeId" => $postLocale->locale_id
                ];
            }

            $hashtags = $post['hashtags'];
            $response['post']['hashtags'] = [];
            foreach ($hashtags as $hashtag) {
                $response['post']['hashtags'][] = $hashtag->id;
            }

            //  All CATEGORY    &   SUBCATEGORY
            $getAllCatSubcat = self::_getAllCategoriesSubcategories();
            $response['categories'] = $getAllCatSubcat['categories'];

            //  All HASHTAGS
            $getAllHashtags = self::_getAllHashtags();
            $response['hashtags'] = $getAllHashtags['hashtags'];

            $response['activeLocales'] = LocaleSettings::getActiveLocales();
            $response['templateDivider'] = self::TEMPLATE_MAX_COLUMNS / count($response['activeLocales']);

            $response['colLength'] = [
                'post' => DBColumnLengthData::getPostLenghts(),
            ];

        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response()
            -> view('admin.posts.crud.update-main-details', ['response' => $response]);
    }

    /**
     * Prepare Post Main Details Info by Post Id
     * Make Changes After Post Main Info Changed
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postMainDetails_post(Request $request, $locale, $id)
    {
        $requestAll =  $request->all();
        $requestAll['postHashtag'] = json_decode($requestAll['postHashtag']);

        // Main fields Validation
        $mainValidation = PostsValidation::updatePostMainFieldsValidations($requestAll);

        if ($mainValidation['error']) {
            return ResponseController::_validationResultResponse($mainValidation);
        }

        try {
            $post = Post::postWithPostLocaleHashtagsById($id);

            // Post Main Edited
            self::_postMainPartEdited($request, $post);

            // Hashtag Attach
            Post::hashtagsEdited(json_decode($request->postHashtag), $post);

        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Prepare Post Part Details Info by Post Id
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function postPartsDetails_get(Request $request, $alias, $id)
    {
        $response = Helpers::prepareAdminNavbars();
        // todo not sure that we need try/catch here
        try {
            $post = Post::postWithPostLocalePostPartsById($id);

            // Post Parts
            foreach ($post['postLocale'] as $localedPost) {
                $postParts = $localedPost['postParts'];
                $localeName = LocaleSettings::getLocaleNameById($localedPost->locale_id);

                foreach ($postParts as $postPart) {
                    $response['post']['postparts'][$localeName][] = [
                        'id' => $postPart->id,
                        'head' => $postPart->head,
                        'body' => $postPart->body,
                        'foot' => $postPart->foot,
                    ];
                }
            }

            $response['colLength'] = [
                'parts' => DBColumnLengthData::POST_PARTS_TABLE,
            ];
            $response['activeLocales'] = LocaleSettings::getActiveLocales();
            $response['templateDivider'] = self::TEMPLATE_MAX_COLUMNS / count($response['activeLocales']);
        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response()
            -> view('admin.posts.crud.update-parts-details', ['response' => $response]);
    }

    /**
     * Update Post Part
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postPartsDetails_post(Request $request)
    {
        $validationResult = PostsValidation::validatePostPartsUpdate($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $postPart = PostParts::postPartsWithPostLocalePostSubcategoryById($request->partId);
            $updateArr = [
                'head' => $request->head,
                'foot' => $request->foot
            ];

            $file = $request->file('body');
            if ($file) {
                $newName = microtime() . '.' . $file->getClientOriginalExtension();
                $updateArr['body'] = $newName;

                $getRes = DirectoryEditor::postPartImageEdit($postPart);
                if (!$getRes['error'] && $getRes['toAddDir']) {
                    $filename = $getRes['toAddDir'] . DIRECTORY_SEPARATOR . $newName;
                    Storage::disk('public_posts')->put($filename, File::get($file));
                }
            }

            $postPart->update($updateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Delete Post Part
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postPartDelete_post(Request $request)
    {
        $validationResult = PostsValidation::validatePostPartDelete($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }
        try {
            $postPart = PostParts::postPartsWithPostLocalePostSubcategoryById($request->partId);
            DirectoryEditor::removePostPartImage($postPart);
            $postPart->delete();
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Delete Post
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postDelete_post(Request $request)
    {
        $validationResult = PostsValidation::validatePostDelete($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }
        
        try {
            $post = Post::findOrFail($request->postId);
            $isRemoved = DirectoryEditor::removePostDir($post);

            if (!$isRemoved['error']) {
                $post->delete();
            }
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * // todo need to remove,
     * Get Post Part by it's ID
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
//    protected function postPartsAttach_get(Request $request, $id)
//    {
//        $response = Helpers::prepareAdminNavbars();
//        // todo not sure that we need try/catch here
//        try {
//            $postPart = PostParts::where('id', $id)->first();
//            $response['postpart'] = [
//                'head' => $postPart->head,
//            ];
//        } catch(\Exception $e) {
//            return ResponseController::_catchedResponse($e);
//        }
//
//        return response()
//            -> view('admin.posts.crud.attach-part', ['response' => $response]);
//    }

    /**
     * todo need to remove
     * Proceed post part attachment to new post
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
//    protected function postPartsAttachSave_post(Request $request, $id)
//    {
//        $validationResult = PostsValidation::postPartsAttachSave($request->all());
//
//        if ($validationResult['error']) {
//            return ResponseController::_validationResultResponse($validationResult);
//        }
//        try {
//            $postPart = PostParts::findOrFail($id);
//            $oldPost = clone $postPart->post()->first();
//            $newPost = Post::findOrFail($request->newPostId);
//            $dirEdited = DirectoryEditor::postPartAttachmentProcess($oldPost, $newPost, $postPart);
//            $updateArr = [
//                'post_id' => $request->newPostId,
//                'body' => $dirEdited['newName']
//            ];
//            $postPart->update($updateArr);
//        } catch (\Exception $e) {
//            return ResponseController::_catchedResponse($e);
//        }
//
//        return response(['error' => false]);
//    }

    /**
     * Generate post main parts (except hashtag) after post main part edit
     * @param $request
     * @param $post
     * @throws \Exception
     */
    private static function _postMainPartEdited($request, $post)
    {
        $postUpdateArr = [];
        $oldInfo['oldPost'] = $post;

        // Subcategory changes
        $subcat = $post->subcategory()->first();
        if ($post->subcateg_id !== (int)$request->postSubcategory) {
            $postUpdateArr['subcateg_id'] = $request->postSubcategory;
            $oldSubcat = $subcat;
            $newSubcat = Subcategory::getSubCategoryBuilderByID($request->postSubcategory)->first();

            $result = DirectoryEditor::updateAfterSubcategoryEditforPost($oldSubcat, $newSubcat, $post);

            if ($result['error']) {
                throw new \Exception("Directory rename Error");
            }

            $subcat = $newSubcat;
        }

        // Alias changes
        $aliasChanged = false;
        if ($post->alias !== $request->postAlias) {
            $aliasChanged = true;
            $oldAlias = $post->alias;
            $newAlias = $request->postAlias;
            $postUpdateArr['alias'] = $newAlias;

            $result = DirectoryEditor::updateAfterPostAliasChanged($subcat, $oldAlias, $newAlias);

            if ($result['error']) {
                throw new \Exception("Directory rename Error");
            }
        }

        // Localed Main information
        $newAlias = $post->alias;
        foreach ($request['header'] as $localeName => $mainItem) { // each localed item
            $updateArr = [
                'header' => $request->header[$localeName],
                'text' => $request->text[$localeName],
            ];

            $postMainImageLocale = isset($request->file('mainImage')[$localeName]) ? $request->file('mainImage')[$localeName] : [];
            $oldPostLocaled = $post['postLocale']
                ->where('locale_id', LocaleSettings::getLocaleIdByName($localeName))
                ->first();

            $imgName = explode('.', $oldPostLocaled->image);
            $ext = end($imgName);

            if ($aliasChanged) {
                // case alias changed, image sent
                if (!empty($postMainImageLocale)) {
                    $ext = $postMainImageLocale->getClientOriginalExtension();
                    $result = DirectoryEditor::emptyPostFiles($subcat, $localeName, $post, $oldPostLocaled);

                    if ($result['error']) {
                        throw new \Exception("Directory rename Error");
                    } else {
                        $filename = $subcat->alias . DIRECTORY_SEPARATOR
                            . $post->alias . DIRECTORY_SEPARATOR
                            . $localeName . DIRECTORY_SEPARATOR
                            . $newAlias . '.' . $ext;
                        Storage::disk('public_posts')->put($filename, File::get($postMainImageLocale));
                    }
                }
                // case alias changed, image didn't sent
                else {
                    $oldImageName = $oldPostLocaled->image;
                    $newImageName = $newAlias . '.' . $ext;
                    $result = DirectoryEditor::movePostFiles($subcat, $localeName, $post, $oldImageName, $newImageName);

                    if ($result['error']) {
                        throw new \Exception("Directory rename Error");
                    }
                }

                $updateArr['image'] = $request->postAlias . '.' . $ext;
            }
            // case alias NOT changed, but image sent
            elseif(!empty($postMainImageLocale)) {
                // update only if new added image have other extension, otherwise we don't need to update in table
                if ($postMainImageLocale->getClientOriginalExtension() !== end($imgName)) {
                    $ext = $postMainImageLocale->getClientOriginalExtension();
                    $updateArr['image'] = $request->postAlias . '.' . $ext;
                }

                $result = DirectoryEditor::emptyPostFiles($subcat, $localeName, $post, $oldPostLocaled);

                if ($result['error']) {
                    throw new \Exception("Directory rename Error");
                } else {
                    $filename = $subcat->alias . DIRECTORY_SEPARATOR
                        . $post->alias . DIRECTORY_SEPARATOR
                        . $localeName . DIRECTORY_SEPARATOR
                        . $newAlias . '.' . $ext;
                    Storage::disk('public_posts')->put($filename, File::get($postMainImageLocale));
                }
            }
            $oldPostLocaled->update($updateArr);
        }
    }

    /**
     * Create Post Main
     * @param $request
     * @return array
     */
    private static function _createPostMain($request)
    {
        $subcategory = Subcategory::findOrFail($request->postSubcategory);

        $createPostArr = [
            'alias' => $request->postAlias
        ];
        $post = $subcategory->posts()->create($createPostArr);

        $createPostPartArr = [];
        $mainPath = $subcategory->alias . DIRECTORY_SEPARATOR . $post->alias;
        $activeLocales = json_decode($request->activeLocales);

        foreach ($activeLocales as $locale) {
            $imgName = $request['postAlias'] . '.' . $request->file('mainImage')[$locale]->getClientOriginalExtension();
            $createPostPartArr[] = [
                'header' => $request['header'][$locale],
                'text' => $request['text'][$locale],
                'image' => $imgName,
                'locale_id' => LocaleSettings::getLocaleIdByName($locale)
            ];
            $file = $request->file('mainImage')[$locale];
            $filename = $mainPath . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . $imgName;
            Storage::disk('public_posts')->put($filename, File::get($file));
        }

        $postsLocale = $post->postLocale()->createMany($createPostPartArr);

        return [
            'post' => $post,
            'postsLocale' => $postsLocale,
            'mainPath' => $mainPath
        ];
    }

    /**
     * Create Post Parts
     * @param $request
     * @param $postsLocale
     * @param $mainPath
     * @return array
     */
    private static function _createPostParts($request, $postsLocale, $mainPath)
    {
        foreach ($postsLocale as $postLocale) {
            $eachCreateArr = [];
            $localeName = LocaleSettings::getLocaleNameById($postLocale->locale_id);
            $partHeaderLocaled = $request->partHeader[$localeName];
            $partImageLocaled = $request->file('partImage')[$localeName];
            $partFooterLocaled = $request->partFooter[$localeName];

            foreach ($partHeaderLocaled as $key => $partHeader) {
                $partImage = $partImageLocaled[$key];
                $bodyImage = microtime() . '.' . $partImage->getClientOriginalExtension(); // todo dont like nicrotime here

                $eachCreateArr[] = [
                    'head'=> $partHeaderLocaled[$key],
                    'body'=> $bodyImage,
                    'foot'=> $partFooterLocaled[$key]
                ];

                $filename = $mainPath . DIRECTORY_SEPARATOR
                    . $localeName . DIRECTORY_SEPARATOR
                    . 'parts' . DIRECTORY_SEPARATOR
                    . $bodyImage;
                Storage::disk('public_posts')->put($filename, File::get($partImage));
            }
            $postParts = $postLocale->postParts()->createMany($eachCreateArr);
        }
        return ['error' => false];
    }

    /**
     * return All Categories and Subcategories
     * @return array
     */
    private static function _getAllCategoriesSubcategories()
    {
        $response = [];
        $categories = Category::getCategoriesWithSubcategories();

        foreach ($categories as $key => $category) {
            $response['categories'][$key]['category'] = [
                'id' => $category->id,
                'alias' => $category->alias
            ];
            $response['categories'][$key]['subcategory'] = [];
            $subcategories = $category['subcategories'];
            foreach ($subcategories as $subcategory) {
                $response['categories'][$key]['subcategory'][] = [
                    'id' => $subcategory->id,
                    'alias' => $subcategory->alias
                ];
            }
        }
        return $response;
    }

    /**
     * return All Hashtags
     * @return array
     */
    private static function _getAllHashtags()
    {
        $response = [];
        $hashtags = Hashtag::getAllHashtags();

        // todo remove it to prepare
        foreach ($hashtags as $hashtag) {
            $response['hashtags'][] = [
                'id' => $hashtag->id,
                'alias' => $hashtag->alias
            ];
        }
        return $response;
    }

    protected static function _postBySearchType($request) {
        switch ($request->searchType) {
            case self::POSTEDITSEARCHTYPES['byID']:
                return Post::getPostBuilderByID($request->searchText);
                break;
            case self::POSTEDITSEARCHTYPES['byAlias']:
                return Post::getPostsBuilderLikeAlias("%$request->searchText%");
                break;
            default:
                throw new \Exception('Can not find. Wrong Post type');
        }
    }
}
