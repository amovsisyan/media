<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Post;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

class CrudController extends PostsController
{
    const POSTEDITSEARCHTYPES = [
        'byID' => '1',
        'byHeader' => '2',
        'byAlias' => '3',
    ];

    protected function createPost_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        //  All CATEGORY    &   SUBCATEGORY
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

        //  All HASHTAGS
        $hashtags = Hashtag::select('id', 'hashtag')->orderBy('hashtag')->get();
        foreach ($hashtags as $hashtag) {
            $response['hashtags'][] = [
                'id' => $hashtag->id,
                'hashtag' => $hashtag->hashtag
            ];
        }

        return response()
            -> view('admin.posts.crud.create', ['response' => $response]);
    }

    protected function createPost_post(Request $request)
    {
        $requestAll =  $request->all();

        // Main fields Validation
        $mainValidation = Validation::createPostMainFieldsValidations($requestAll);
        if ($mainValidation['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $mainValidation['type'],
                    'response' => $mainValidation['response']
                ], 404
            );
        }

        // Part fields Validation
        $partValidation = Validation::createPostPartFieldsValidations($requestAll);
        if ($partValidation['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $partValidation['type'],
                    'response' => $partValidation['response']
                ], 404
            );
        }

        try {
            //ToDo Make 3 separate private methods for this 3 parts

            // Post Main Creation
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


            // Post Parts Creation
            $createArr = [];

            foreach ($requestAll['partHeader'] as $key => $value) {
                $createArr[$key] = [
                    'head' => $requestAll['partHeader'][$key],
                    'body' => $post->alias . '_' . $key . '.' . $request->file('partImage')[$key]->getClientOriginalExtension(),
                    'foot' => $requestAll['partFooter'][$key],
                ];
            };

            $postParts = $post->postParts()->createMany($createArr);

            foreach ($requestAll['partImage'] as $key => $file) {
                $filename = $mainPath . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . $postParts[$key]->body;
                Storage::disk('public_posts')->put($filename, File::get($file));
            };

            // Hashtag Attach
            $post->hashtags()->attach(json_decode($request->postHashtag));

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

    protected function updatePost_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.posts.crud.update', ['response' => $response]);
    }

    protected function updatePost_post(Request $request)
    {
        $validationResult = Validation::validateEditPostSearchValues($request->all());
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
            case self::POSTEDITSEARCHTYPES['byID']:
                $post = Post::where('id', $request->searchText);
                break;
            case self::POSTEDITSEARCHTYPES['byHeader']:
                $post = Post::where('header', 'like', "%$request->searchText%");
                break;
            default:
                $post = Post::where('alias', 'like', "%$request->searchText%");
        }

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

    protected function updatePostDetails_post(Request $request)
    {
        $validationResult = Validation::validateEditPostDetailsValues($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        $response = [];
        try {
            $post = Post::where('id', $request->postId)->first();
            $response['post'] = [
                "id" => 1,
                "alias" => $post->alias,
                "header" => $post->header,
                "text" => $post->text,
                "image" => $post->image,
                "subcateg_id" => $post->subcateg_id
            ];

            $hashtags = $post->hashtags()->select('hashtags.id')->get();
            foreach ($hashtags as $hashtag) {
                $response['post']['hashtags'][] = $hashtag->id;
            }

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

                // todo this all and hashtag all is doubleing , once these are used also in createPost_get()
            //  All CATEGORY    &   SUBCATEGORY
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

            //  All HASHTAGS
            $hashtags = Hashtag::select('id', 'hashtag')->orderBy('hashtag')->get();
            foreach ($hashtags as $hashtag) {
                $response['hashtags'][] = [
                    'id' => $hashtag->id,
                    'hashtag' => $hashtag->hashtag
                ];
            }
        } catch(\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        return response(
            [
                'error' => false,
                'response' => $response
            ]
        );
    }
}
