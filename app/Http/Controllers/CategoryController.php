<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    const RECENT_POSTS_COUNT = 5;

    public function __construct()
    {
    }

    protected function getCategory(Request $request)
    {
        $posts = Post::orderBy('created_at', 'desc')->take(self::RECENT_POSTS_COUNT)->get();

        // the same part written in \App\Http\Controllers\SubcategoryController::getByHashtag
        $respPosts = [];
        foreach($posts as $key => $post){
            $subCategory = $post->subcategory()->select('alias', 'categ_id')->first();
            $category = $subCategory->category()->select('alias')->first();
            $respPosts[] = [
                'id'        => $post->id,
                'alias'     => $post->alias,
                'header'    => $post->header,
                'text'      => $post->text,
                'image'     => $post->image,
                'sub_id'    => $post->subcateg_id,
                'sub_alias' => $subCategory->alias,
                'categ_id'  => $subCategory->categ_id,
                'cat_alias' => $category->alias
            ];
        }

        $response = [
            'navbar'    => $this->getNavbar(),
            'posts'     => $respPosts,
        ];

        return response()
            -> view('welcome', ['response' => $response]);
    }

    public function getNavbar ()
    {
        $categories = Category::select('id', 'alias', 'name')->get();

        $response = [];
        foreach ($categories as $key => $category) {
            $response[$key]['category'] = [
                'alias'     => $category->alias,
                'name'      => $category->name,
            ];
            $subcategories = $category->subcategories()->get();
            foreach ($subcategories as $subcategory) {
                $response[$key]['subcategory'][] = [
                    'id'        => $subcategory->id,
                    'alias'     => $subcategory->alias,
                    'name'      => $subcategory->name,
                ];
            }
        }

        return  $response;
    }
}
