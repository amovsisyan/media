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

    protected function getSubCategory (Request $request, $category, $subcategory)
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
        // ToDo Rewrite this method I think it is not fast
        $hashtag = Hashtag::where('alias', $alias)->get();

        $posts = $hashtag[0]->posts()->get();

        foreach($posts as $key => $post){
            $sub_category = $post->subcategory()->select('id', 'alias', 'categ_id')->first();
            $posts[$key]['sub_alias'] = $sub_category->alias;
            $posts[$key]['sub_id'] = $sub_category->id;
            $posts[$key]['cat_alias'] = $sub_category->category()->select('alias')->first()->alias;
        }

        $response = [
            'navbar'    => $this->getNavbar(),
            'posts'     => $posts,
            'hashtag'   => $hashtag[0]->hashtag
        ];

        return response()
            -> view('by-hashtag', ['response' => $response]);
    }
}
