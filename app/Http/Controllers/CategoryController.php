<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    protected function getCategory (Request $request)
    {
        $posts = Post::orderBy('created_at', 'desc')->take(5)->get();

        foreach($posts as $key => $post){
            $sub_category = $post->subcategory()->select('id', 'alias', 'categ_id')->first();
            $posts[$key]['sub_alias'] = $sub_category->alias;
            $posts[$key]['sub_id'] = $sub_category->id;
            $posts[$key]['cat_alias'] = $sub_category->category()->select('alias')->first()->alias;
        }

        $response = [
            'navbar'    => $this->getNavbar(),
            'posts'     => $posts,
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
