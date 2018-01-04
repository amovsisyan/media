<?php

namespace App\Http\Controllers;

use App\Hashtag;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Http\Controllers\Services\SEO\SEOService;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    public function getByHashtag(Request $request, $locale, $alias)
    {
        $postsLocaleByHashtagAlias = Hashtag::getPostsLocaledByHashtagAlias($alias);

        $respPosts = ResponsePrepareHelper::PR_GetByHashtag($postsLocaleByHashtagAlias);

        $response = [
              'navbar'  => Helpers::getNavbar()
            , 'seo'     => SEOService::getHashtagSEOKeys($respPosts)
            , 'posts'   => $respPosts['data']
            , 'hashtag' => $respPosts['hashtagsLocale']
        ];

        return response()
            -> view('by-hashtag', ['response' => $response]);
    }
}
