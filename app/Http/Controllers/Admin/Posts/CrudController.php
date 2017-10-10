<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Http\Controllers\Helpers\DirectoryEditor;
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
        'byHeader' => '2',
        'byAlias' => '3',
    ];

    /**
     * Returns Data for Post Creation View
     * @return \Illuminate\Http\Response
     */
    protected function createPost_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        //  All CATEGORY    &   SUBCATEGORY
        $getAllCatSubcat = self::_getAllCategoriesSubcategories();
        $response['categories'] = !empty($getAllCatSubcat) && !empty($getAllCatSubcat['categories']) ? $getAllCatSubcat['categories'] : [];

        //  All HASHTAGS
        $getAllHashtags = self::_getAllHashtags();
        $response['hashtags'] = !empty($getAllHashtags['hashtags']) ? $getAllHashtags['hashtags'] : [];

        $response['colLength'] = [
            'post' => DBColumnLengthData::POSTS_TABLE,
            'parts' => DBColumnLengthData::POST_PARTS_TABLE,
        ];

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
        $requestAll =  $request->all();

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
            $mainPath = $createMainRes['mainPath'];

            // Post Parts Creation
            self::_createPostParts($request, $post, $mainPath);

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
    protected function postAddNewParts_post(Request $request, $id)
    {
        $requestAll = $request->all();

        // Part fields Validation
        $partValidation = PostsValidation::createPostPartFieldsValidations($requestAll);
        if ($partValidation['error']) {
            return ResponseController::_validationResultResponse($partValidation);
        }

        try {
            $post = Post::findOrFail($id);
            $subcategory = $post->subcategory()->first();
            $mainPath = $subcategory->alias . '_' . $subcategory->id . DIRECTORY_SEPARATOR . $post->alias . '_' . $post->id;

            // Post Parts Creation
            self::_createPostParts($request, $post, $mainPath);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    // todo rename this method and add annotation
    protected function updatePost_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

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

        $searchResult = $post->select('id', 'header', 'text')->get();
        $response['posts'] = [];
        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $response['posts'][] = [
                    'id' => $item->id,
                    'header' => $item->header,
                    'text' => $item->text
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
    protected function postMainDetails_get(Request $request, $id)
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        // todo not sure that we need try/catch here
        try {
            $post = Post::where('id', $id)->first();
            $response['post'] = [
                "id" => $post->id,
                "alias" => $post->alias,
                "header" => $post->header,
                "text" => $post->text,
                "image" => $post->image,
                "subcateg_id" => $post->subcateg_id
            ];

            $hashtags = $post->hashtags()->select('hashtags.id')->get();
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
    protected function postMainDetails_post(Request $request, $id)
    {
        $requestAll =  $request->all();

        // Main fields Validation
        $mainValidation = PostsValidation::updatePostMainFieldsValidations($requestAll);
        if ($mainValidation['error']) {
            return ResponseController::_validationResultResponse($mainValidation);
        }

        try {
            $post = Post::findOrFail($id);

            // Post Main Edited
            self::_postMainPartEdited($request, $post);

            // Hashtag Attach
            self::_postHashtagsEdited($request, $post);

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
    protected function postPartsDetails_get(Request $request, $id)
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        // todo not sure that we need try/catch here
        try {
            $post = Post::where('id', $id)->first();
            // Post Parts
            $postParts = $post->postParts()->select('post_parts.id', 'head', 'body', 'foot')->get();
            foreach ($postParts as $postPart) {
                $response['post']['postparts'][] = [
                    'id' => $postPart->id,
                    'head' => $postPart->head,
                    'body' => $postPart->body,
                    'foot' => $postPart->foot,
                ];
            }
            $response['colLength'] = [
                'parts' => DBColumnLengthData::POST_PARTS_TABLE,
            ];
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
            $postPart = PostParts::findOrFail($request->partId);
            $oldPostPart = clone $postPart;
            $updateArr = [
                'head' => $request->head,
                'foot' => $request->foot
            ];
            $newName = $oldPostPart->body;

            $imgUpdated = false;
            $file = $request->file('body');
            if ($file) {
                $imgUpdated = true;
                $imgName = explode('.', $oldPostPart->body);
                $ext = end($imgName);
                if ($ext !== $file->getClientOriginalExtension()) {
                    $newName = [
                        $imgName[0],  $file->getClientOriginalExtension()
                    ];
                    $updateArr['body'] = implode('.', $newName);
                }
            }
            $postPart->update($updateArr);
            if ($imgUpdated) {
                $getRes = DirectoryEditor::postPartImageEdit($postPart, $oldPostPart);
                if (!$getRes['error'] && $getRes['toAddDir']) {
                    $filename = $getRes['toAddDir'] . $newName;
                    Storage::disk('public_posts')->put($filename, File::get($file));
                }
            }
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
            $postPart = PostParts::findOrFail($request->partId);
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
            DirectoryEditor::removePostDir($post);
            $post->delete();
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Get Post Part by it's ID
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    protected function postPartsAttach_get(Request $request, $id)
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));
        // todo not sure that we need try/catch here
        try {
            $postPart = PostParts::where('id', $id)->first();
            $response['postpart'] = [
                'head' => $postPart->head,
            ];
        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response()
            -> view('admin.posts.crud.attach-part', ['response' => $response]);
    }

    /**
     * Proceed post part attachment to new post
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function postPartsAttachSave_post(Request $request, $id)
    {
        $validationResult = PostsValidation::postPartsAttachSave($request->all());
        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }
        try {
            $postPart = PostParts::where('id', $id)->first();
            $oldPost = clone $postPart->post()->first();
            $newPost = Post::where('id', $request->newPostId)->first();
            $dirEdited = DirectoryEditor::postPartAttachmentProcess($oldPost, $newPost, $postPart);
            $updateArr = [
                'post_id' => $request->newPostId,
                'body' => $dirEdited['newName']
            ];
            $postPart->update($updateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    /**
     * Generate post main parts (except hashtag) after post main part edit
     * @param $request
     * @param $post
     * @throws \Exception
     */
    private static function _postMainPartEdited($request, $post)
    {
        $oldPost = clone $post;
        $oldInfo['oldPost'] = $post;

        $updateArr = [
            'alias' => $request->postAlias,
            'header' => $request->postMainHeader,
            'text' => $request->postMainText,
        ];

        $subcategCHANGED = false;
        $oldSubcat = [];
        if ($post->subcateg_id !== (int)$request->postSubcategory) {
            $updateArr['subcateg_id'] = $request->postSubcategory;
            $oldSubcat = $post->subcategory()->first();
            $subcategCHANGED = true;
        }


        $postMainImageCHANGED = false;
        $postAliasCHANGED = false;
        $postMainImage = $request->file('postMainImage');
        $imgName = explode('.', $post->image);
        if ($post->alias !== $request->postAlias) {
            // case alias changed, image sent
            $postAliasCHANGED = true;
            if ($postMainImage) {
                $ext = $postMainImage->getClientOriginalExtension();
                $postMainImageCHANGED = true;
            }
            // case alias changed, image didn't sent
            else {
                $ext = end($imgName);
            }
            $updateArr['image'] = $request->postAlias . '.' . $ext;
        }
        // case alias NOT changed, but image sent
        elseif($postMainImage) {
            $postMainImageCHANGED = true;
            // update only if new added image have other extension, otherwise we don't need to update in table
            if ($postMainImage->getClientOriginalExtension() !== end($imgName)) {
                $ext = $postMainImage->getClientOriginalExtension();
                $updateArr['image'] = $request->postAlias . '.' . $ext;
            }
        }

        $updatedPost = $post->update($updateArr);

        if ($updatedPost) {
            $newSubcat = $post->subcategory()->first();
            if ($subcategCHANGED && !empty($oldSubcat)) {
                $result = DirectoryEditor::updateAfterSubcategoryEditforPost($oldSubcat, $newSubcat, $oldPost);
                if ($result['error']) {
                    throw new \Exception("Directory rename Error");
                }
            }

            if ($postAliasCHANGED) {
                $result = DirectoryEditor::updateAfterAliasEditedforPost($newSubcat, $oldPost, $post);
                if ($result['error']) {
                    throw new \Exception("Directory rename Error");
                }
            }

            if ($postMainImageCHANGED) {
                $postPath = $newSubcat->alias . '_' . $newSubcat->id . DIRECTORY_SEPARATOR . $post->alias . '_' . $post->id;
                $postFilesDir =  DIRECTORY_SEPARATOR . DirectoryEditor::IMGCATPATH . DIRECTORY_SEPARATOR . $postPath;
                File::delete(File::files($postFilesDir));
                $file = $request->file('postMainImage');
                $filename = $postPath . DIRECTORY_SEPARATOR . $post->image;
                Storage::disk('public_posts')->put($filename, File::get($file));
            }
        }
    }

    /**
     * Generate hashtags after post Main part edit
     * @param $request
     * @param $post
     */
    private static function _postHashtagsEdited($request, $post)
    {
        $post->hashtags()->detach();
        $post->hashtags()->attach(json_decode($request->postHashtag));
    }

    /**
     * Create Post Main
     * @param $request
     * @return array
     */
    private static function _createPostMain($request)
    {
        $subcategory = Subcategory::findOrFail($request->postSubcategory);

        $post = $subcategory->posts()->create(
            [
                'alias' => $request->postAlias,
                'header' => $request->postMainHeader,
                'text' => $request->postMainText,
                'image' => $request->postAlias . '.' . $request->file('postMainImage')->getClientOriginalExtension()
            ]
        );
        $mainPath = $subcategory->alias . '_' . $subcategory->id . DIRECTORY_SEPARATOR . $post->alias . '_' . $post->id;
        $file = $request->file('postMainImage');
        $filename = $mainPath . DIRECTORY_SEPARATOR . $post->image;
        Storage::disk('public_posts')->put($filename, File::get($file));

        return [
            'post' => $post,
            'mainPath' => $mainPath
        ];
    }

    /**
     * Create Post Parts
     * @param $request
     * @param $post
     * @param $mainPath
     * @return array
     */
    private static function _createPostParts($request, $post, $mainPath)
    {
        $createArr = [];
        $postParts = $post->postParts()->get();

        // this needs for update, it helps not overwrite existing images (image names)
        $arrOfBusyNums = [];
        if ($postParts->count()) {
            foreach ($postParts as $part) {
                $explodedOnce =  explode('_', $part->body);
                $lastPart = end($explodedOnce);
                $arrOfBusyNums[] = explode('.', $lastPart)[0];
            }
        };

        foreach ($request['partHeader'] as $key => $value) {
            if (in_array($key, $arrOfBusyNums)) {
                $bodyKey = max($arrOfBusyNums) + 1;
                $arrOfBusyNums[] = $bodyKey;
            } else {
                $bodyKey = $key;
            }
            $createArr[$key] = [
                'head' => $request['partHeader'][$key],
                'body' => $post->alias . '_' . $bodyKey . '.' . $request->file('partImage')[$key]->getClientOriginalExtension(),
                'foot' => $request['partFooter'][$key],
            ];
        };

        $postParts = $post->postParts()->createMany($createArr);

        foreach ($request['partImage'] as $key => $file) {
            $filename = $mainPath . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . $postParts[$key]->body;
            Storage::disk('public_posts')->put($filename, File::get($file));
        };
        return ['error' => false];
    }

    /**
     * return All Categories and Subcategories
     * @return array
     */
    private static function _getAllCategoriesSubcategories()
    {
        $response = [];
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        foreach ($categories as $key => $category) {
            $response['categories'][$key]['category'] = [
                'id' => $category->id,
                'name' => $category->name
            ];
            $response['categories'][$key]['subcategory'] = [];
            $subcategories = $category->subcategories()->select('id', 'name')->get();
            foreach ($subcategories as $subcategory) {
                $response['categories'][$key]['subcategory'][] = [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name
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
        $hashtags = Hashtag::select('id', 'hashtag')->orderBy('hashtag')->get();
        foreach ($hashtags as $hashtag) {
            $response['hashtags'][] = [
                'id' => $hashtag->id,
                'hashtag' => $hashtag->hashtag
            ];
        }
        return $response;
    }

    protected static function _postBySearchType($request) {
        switch ($request->searchType) {
            case self::POSTEDITSEARCHTYPES['byID']:
                return Post::where('id', $request->searchText);
                break;
            case self::POSTEDITSEARCHTYPES['byHeader']:
                return Post::where('header', 'like', "%$request->searchText%");
                break;
            default:
                return Post::where('alias', 'like', "%$request->searchText%");
        }
    }
}
