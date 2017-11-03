<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\PostLocale;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    const RECENT_POSTS_COUNT = 6;

    public function __construct()
    {
    }

    protected function getCategory(Request $request)
    {
        $postsLocale = PostLocale::getLimitedLocalizedPosts(self::RECENT_POSTS_COUNT);

        $respPostsLocale = ResponsePrepareHelper::PR_GetCategory($postsLocale);

        $response = [
            'navbar' => Helpers::getNavbar()
            , 'posts' => $respPostsLocale
        ];

        return response()
            -> view('welcome', ['response' => $response]);
    }
}
