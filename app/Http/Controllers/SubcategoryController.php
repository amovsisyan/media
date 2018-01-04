<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Http\Controllers\Services\Pagination\PaginationService;
use App\Http\Controllers\Services\SEO\SEOService;
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
            'navbar'     => Helpers::getNavbar(),
            'seo'        => SEOService::getSubcategorySEOKeys($respPosts),
            'posts'      => $respPosts,
            'pagination' => PaginationService::makeSubcategoryPagination($subcategoryPostsLocale)
        ];

        return response()
            -> view('category', ['response' => $response]);
    }
}
