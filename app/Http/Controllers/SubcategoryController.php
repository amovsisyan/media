<?php

namespace App\Http\Controllers;

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
        $alias = $subcategory;
        $subcategoryPostsLocale = Subcategory::getSubcategoryPostsLocaledByAlias($alias);

        $respPosts = ResponsePrepareHelper::PR_GetSubCategory($subcategoryPostsLocale);

        $response = [
            'navbar'    => Helpers::getNavbar(),
            'posts'     => $respPosts
        ];

        return response()
            -> view('category', ['response' => $response]);
    }
}
