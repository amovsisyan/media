<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Post;
use Illuminate\Http\Request;

class PostController extends SubcategoryController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPost(Request $request, $category, $subcategory, $post)
    {
        $post = Post::where('alias', $post)->first();
        if (!empty($post)) {
            $postParts = ResponsePrepareHelper::PR_partsGetPost($post);
            $postHashtags = ResponsePrepareHelper::PR_hashtagsGetPost($post);

            $response = [
                'navbar'      => Helpers::getNavbar(),
                'post_header' => $post->header,
                'post_parts'  => $postParts,
                'hashtags'    => $postHashtags
            ];
        }

        return response()
            -> view('current-post', ['response' => $response]);
    }
}
