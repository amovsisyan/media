<?php

namespace App\Http\Controllers\Services\SEO;

/**
 * This class used for making SEO keys for page types
 * Class SEOService
 * @package App\Http\Controllers\Services\SEO
 */
class SEOService
{
    /**
     * SEO keys for subcategory page
     * @param null $respPosts
     * @return array
     */
    public static function getSubcategorySEOKeys($respPosts = null)
    {
        $SEOArray = self::getDefaultSEOKeys();

        if ($respPosts) {
            $length = (($count = count($respPosts)) > 3) ? 3 : $count - 1;
            for ($i=0; $i<= $length; $i++) {
                $SEOArray['description'] .= $respPosts[$i]['header'] . ', ';
                $SEOArray['keywords'] .= $respPosts[$i]['alias'] . ', ';
            };
        }

        return $SEOArray;
    }

    /**
     * SEO keys for Current post page
     * @param null $postPartsResponse
     * @return array
     */
    public static function getPostSEOKeys($postPartsResponse = null)
    {
        $SEOArray = self::getDefaultSEOKeys();

        if ($postPartsResponse) {
            $SEOArray['title'] .= $postPartsResponse['postHeader'];
            $SEOArray['description'] .= $postPartsResponse['postHeader'];
            foreach ($postPartsResponse['data']['postHashtags'] as $hashtag) {
                $SEOArray['keywords'] .= $hashtag['hashtag'] . ', ';
            }
        }

        return $SEOArray;
    }

    /**
     * SEO keys for Hashtag page
     * @param null $respPosts
     * @return array
     */
    public static function getHashtagSEOKeys($respPosts = null)
    {
        $SEOArray = self::getDefaultSEOKeys();

        if ($respPosts) {
            $SEOArray['title'] .= $respPosts['hashtagsLocale'];
            $SEOArray['keywords'] .= $respPosts['hashtagsLocale'] . ', ';
            $length = (($count = count($respPosts['data'])) > 3) ? 3 : $count - 1;
            for ($i=0; $i<= $length; $i++) {
                $SEOArray['description'] .= $respPosts['data'][$i]['header'] . ', ';
                $SEOArray['keywords'] .= $respPosts['data'][$i]['alias'] . ', ';
            };
        }

        return $SEOArray;
    }

    /**
     * Default array for seo keys
     * @return array
     */
    private static function getDefaultSEOKeys()
    {
        return $SEOArray = [
            'title' => '',
            'description' => '',
            'keywords' => ''
        ];
    }
}
