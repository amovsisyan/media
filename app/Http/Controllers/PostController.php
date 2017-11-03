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

    public function getPost(Request $request, $locale, $category, $subcategory, $post)
    {
        $response = [];

        $postPartsLocale = Post::postPartsWithHashtagsLocale($post);

        if (!empty($post)) {
            $postPartsResponse = ResponsePrepareHelper::PR_partsGetPost($postPartsLocale);

            $response = [
                  'navbar'      => Helpers::getNavbar()
                , 'post_header' => $postPartsResponse['postHeader']
                , 'post_parts'  => $postPartsResponse['data']['postParts']
                , 'hashtags'    => $postPartsResponse['data']['postHashtags']
            ];
        }

        return response()
            ->view('current-post', ['response' => $response]);
    }
}
