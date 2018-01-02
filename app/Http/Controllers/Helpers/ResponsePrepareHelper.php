<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Locale\LocaleSettings;

class ResponsePrepareHelper extends Controller
{
    public static function PR_GetCategory($postsLocale)
    {
        $respPosts = [];
        foreach($postsLocale as $key => $postLocale) {
            $subCategory = $postLocale['post']['subcategory'];
            $category = $subCategory['category'];

            $respPosts[] = [
                  'id'        => $postLocale->id
                , 'alias'     => $postLocale['post']->alias
                , 'header'    => $postLocale->header
                , 'text'      => $postLocale->text
                , 'image'     => $postLocale->image
                , 'sub_alias' => $subCategory->alias
                , 'cat_alias' => $category->alias
            ];
        }
        return $respPosts;
    }

    public static function PR_GetByHashtag($postsLocaleByHashtagAlias)
    {
        $respPosts = [];
        $hashtagLocale = null;

        foreach($postsLocaleByHashtagAlias as $key => $hashtag) {
            $hashtagLocale = $hashtag['hashtagsLocale'][0]->hashtag;
            $posts = $hashtag['posts'];
            foreach ($posts as $post) {
                $postsLocale = $post['postLocale'];
                $subcategory = $post['subcategory'];
                $category = $subcategory['category'];

                foreach ($postsLocale as $postLocale) {
                    $respPosts['data'][] = [
                          'id'        => $postLocale->id
                        , 'alias'     => $postLocale['post']->alias
                        , 'header'    => $postLocale->header
                        , 'text'      => $postLocale->text
                        , 'image'     => $postLocale->image
                        , 'sub_alias' => $subcategory->alias
                        , 'cat_alias' => $category->alias
                    ];
                }
            }
        }
        $respPosts['hashtagsLocale'] = $hashtagLocale;
        return $respPosts;
    }

    public static function PR_partsGetPost($postPartsLocale)
    {
        $postPartsResponse = [];
        $postHeader = null;

        foreach ($postPartsLocale['postLocale'] as $postsLocale) {
            $postHeader = $postsLocale->header;
            foreach ($postsLocale['postParts'] as $part) {
                $postPartsResponse['data']['postParts'][] = [
                      'head' => $part->head
                    , 'body' => $part->body
                    , 'foot' => $part->foot
                ];
            }
        }

        $postPartsResponse['data']['postHashtags'] = [];
        foreach ($postPartsLocale['hashtags'] as $hashtag) {
            foreach ($hashtag['hashtagsLocale'] as $hashtagLocale) {
                $postPartsResponse['data']['postHashtags'][] = [
                      'alias'   => $hashtag->alias
                    , 'hashtag' => $hashtagLocale->hashtag
                ];
            }
        }

        $postPartsResponse['postHeader'] = $postHeader;

        return $postPartsResponse;
    }

    public static function PR_GetSubCategory($subcategoryPostsLocale)
    {
        $respPosts = [];
        if ($subcategoryPostsLocale && isset($subcategoryPostsLocale['posts'])) {
            $posts = $subcategoryPostsLocale['posts'];

            foreach ($posts as $post) {
                $postsLocale = $post['postLocale'];

                foreach ($postsLocale as $postLocale) {
                    $respPosts[] = [
                        'alias'  => $post->alias
                        , 'image'  => $postLocale->image
                        , 'header' => $postLocale->header
                        , 'text'   => $postLocale->text
                    ];
                }
            }
        }

        return $respPosts;
    }

    public static function PR_DeleteCategory($categories)
    {
        $response = [];
        foreach ($categories as $category) {
            $response[] = [
                'id' => $category->id,
                'alias' => $category->alias
            ];
        }
        return $response;
    }

    public static function PR_EditHashtag($hashtags)
    {
        $response['hashtags'] = [];
        if (!empty($hashtags)) {
            foreach ($hashtags as $hashtag) {
                $localeArr = [];
                $hashtagLocale = $hashtag['hashtagsLocale'];
                foreach ($hashtagLocale as $locale) {
                    $abbr = LocaleSettings::getLocaleNameById($locale->locale_id);
                    $localeArr[] = [
                        'id' => $locale->id,
                        'hashtag' => $locale->hashtag,
                        'abbr' => $abbr
                    ];
                }
                $response['hashtags'][] = [
                    'id' => $hashtag->id,
                    'alias' => $hashtag->alias,
                    'locale' => $localeArr
                ];
            }
        }

        return $response;
    }

}
