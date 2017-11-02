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
        $localeId = session()->get('localeId', 1); // todo should make some locale helper

        $postPartsLocale = Post::postPartsWithHashtagsLocale($post, $localeId);

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
