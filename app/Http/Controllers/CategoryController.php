<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Http\Controllers\Services\Pagination\PaginationService;
use App\PostLocale;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    protected function getCategory(Request $request)
    {
        $postsLocale = PostLocale::getLimitedLocalizedPosts(PaginationService::getWelcomePerPage());
        $respPostsLocale = ResponsePrepareHelper::PR_GetCategory($postsLocale);

        $response = [
            'navbar' => Helpers::getNavbar()
            , 'posts' => $respPostsLocale
            , 'pagination' => PaginationService::makeWelcomePagination($postsLocale)
        ];

        return response()
            -> view('welcome', ['response' => $response]);
    }
}
