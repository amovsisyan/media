<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Category;
use App\Hashtag;
use App\Http\Controllers\Helpers;
use Illuminate\Http\Request;

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
        // ToDO Part
        var_dump($_FILES);
        dd($_POST);
        $file = $request->file('post_main_image');
        dd($request->file());
        return response()
            -> view('admin.posts.crud.create');
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
