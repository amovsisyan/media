<?php

namespace App\Http\Controllers;

use App\Hashtag;
use App\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends CategoryController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getSubCategory(Request $request, $category, $subcategory)
    {
        $expl_subcat = explode('_', $subcategory);
        $sub_cat_id = $expl_subcat[count($expl_subcat)-1];

        $posts = Subcategory::findOrFail($sub_cat_id)->posts()->get();

        $response = [
            'navbar'    => $this->getNavbar(),
            'posts'     => $posts
        ];

        return response()
            -> view('category', ['response' => $response]);
    }

    public function getByHashtag(Request $request, $alias)
    {
        $hashtag = Hashtag::where('alias', $alias)->first();
        $posts = $hashtag->posts()->get();

        // the same part written in \App\Http\Controllers\CategoryController::getCategory
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
            'hashtag'   => $hashtag->hashtag
        ];

        return response()
            -> view('by-hashtag', ['response' => $response]);
    }
}
