<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
