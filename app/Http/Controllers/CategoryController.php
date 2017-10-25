<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    const RECENT_POSTS_COUNT = 6;

    public function __construct()
    {
    }

    protected function getCategory(Request $request)
    {
        $posts = Post::orderBy('created_at', 'desc')->take(self::RECENT_POSTS_COUNT)->get();

        $respPosts = ResponsePrepareHelper::PR_GetCategory($posts);

        $response = [
            'navbar'    => Helpers::getNavbar(),
            'posts'     => $respPosts,
        ];

        return response()
            -> view('welcome', ['response' => $response]);
    }
}
