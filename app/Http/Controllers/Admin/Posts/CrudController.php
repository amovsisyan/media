<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Http\Controllers\Helpers;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Validator;
use File;

class CrudController extends PostsController
{
    protected function createPost_get()
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

        //  HASHTAGS
        $response['hashtags'] = Hashtag::select('id', 'hashtag')->orderBy('hashtag')->get();

        return response()
            -> view('admin.posts.crud.create', ['response' => $response]);
    }

    protected function createPost_post(Request $request)
    {
        $requestAll =  $request->all();

        // Main fields Validation
        $mainValidation = $this->createPostMainFieldsValidations($requestAll);
        if($mainValidation['error']) {
            return response(
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $mainValidation['response']
                ], 404
            );
        }

        // Part fields Validation
        $partValidation = $this->createPostPartFieldsValidations($requestAll);
        if($partValidation['error']) {
            return response(
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $partValidation['response']
                ], 404
            );
        }

        try {
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
            $mainPath = $subcategory->alias . '_' . $subcategory->id . '/' . $post->alias . '_' . $post->id;
            $file = $request->file('postMainImage');
            $filename = $mainPath . '/' . $post->image;
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
                $filename = $mainPath . '/parts/' . $postParts[$key]->body;
                Storage::disk('public_posts')->put($filename, File::get($file));
            };

            // Hashtag Attach
            $hashtagIds = [];
            foreach (json_decode($request->postHashtag) as $k => $hashtag) {
                $hashtagIds[] = Helpers::explodeGetLast($hashtag);
            }
            $post->hashtags()->attach($hashtagIds);

        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'other_error' => true,
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        return response(['error' => false]);
    }

    // ToDO Make separate validator classHelper, where will be all validation methods
    protected function createPostMainFieldsValidations($req) {
        $rules = [
            'postAlias' => 'required|min:2|max:60',
            'postMainHeader' => 'required|min:2|max:60',
            'postMainText' => 'required|min:2|max:60',
            'postMainImage' => 'required|image ',
            'postSubcategory' => 'required',
            'postHashtag' => 'required',
        ];

        $validator = Validator::make($req, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = [];
            foreach ($errors->all() as $message) {
                $response[] = $message;
            }
            return
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $response
                ];
        }
        return true;
    }

    protected function createPostPartFieldsValidations($req) {
        $rules = [];

        foreach ($req['partHeader'] as $key => $value) {
            $k = 'partHeader.' . $key;
            $rules[$k] = 'required|max:300';
        };

        foreach ($req['partImage'] as $key => $value) {
            $k = 'partImage.' . $key;
            $rules[$k] = 'required|image';
        };

        foreach ($req['partFooter'] as $key => $value) {
            $k = 'partFooter.' . $key;
            $rules[$k] = 'required|max:300';
        };

        $validator = Validator::make($req, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = [];
            foreach ($errors->all() as $message) {
                $response[] = $message;
            }
            return
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $response
                ];
        }
        return true;
    }

    protected function delete()
    {
        return response()
            -> view('admin.posts.crud.delete');
    }

    protected function update()
    {
        return response()
            -> view('admin.posts.crud.update');
    }
}
