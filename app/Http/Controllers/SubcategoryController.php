<?php

namespace App\Http\Controllers;

use App\Hashtag;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends CategoryController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getSubCategory(Request $request, $locale, $category, $subcategory)
    {
        $localeId = session()->get('localeId', 1); // todo should make some locale helper
        // todo or this should go to model controller main

        $alias = $subcategory;
        $subcategoryPostsLocale = Subcategory::getSubcategoryPostsLocaledByAlias($alias, $localeId);

        $respPosts = ResponsePrepareHelper::PR_GetSubCategory($subcategoryPostsLocale);

        $response = [
            'navbar'    => Helpers::getNavbar(),
            'posts'     => $respPosts
        ];

        return response()
            -> view('category', ['response' => $response]);
    }

    public function getByHashtag(Request $request, $alias)
    {
        $hashtag = Hashtag::where('alias', $alias)->first();
        $posts = $hashtag->posts()->get();

        $respPosts = ResponsePrepareHelper::PR_GetCategory($posts);

        $response = [
            'navbar'    => Helpers::getNavbar(),
            'posts'     => $respPosts,
            'hashtag'   => $hashtag->hashtag
        ];

        return response()
            -> view('by-hashtag', ['response' => $response]);
    }
}
