<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;

class ResponsePrepareHelper extends Controller
{
    public static function PR_GetCategory($posts)
    {
        $respPosts = [];
        foreach($posts as $key => $post) {
            $subCategory = $post->subcategory()->select('alias', 'categ_id')->first();
            $category = $subCategory->category()->select('alias')->first();
            $respPosts[] = [
                'id'        => $post->id,
                'alias'     => $post->alias,
                'header'    => $post->header,
                'text'      => $post->text,
                'image'     => $post->image,
                'sub_alias' => $subCategory->alias,
                'cat_alias' => $category->alias
            ];
        }
        return $respPosts;
    }

    public static function PR_partsGetPost($post)
    {
        $parts = $post->postParts()->get();
        $postParts = [];
        foreach ($parts as $part) {
            $postParts[] = [
                'head' => $part->head,
                'body' => $part->body,
                'foot' => $part->foot
            ];
        }
        return $postParts;
    }

    public static function PR_hashtagsGetPost($post)
    {
        $hashtags = $post->hashtags()->get();
        $postHashtags = [];
        foreach ($hashtags as $hashtag) {
            $postHashtags[] = [
                'alias' => $hashtag->alias,
                'hashtag' => $hashtag->hashtag
            ];
        }
        return $postHashtags;
    }

    public static function PR_GetSubCategory($posts)
    {
        $respPosts = [];
        foreach ($posts as $post) {
            $respPosts[] = [
                'alias'  => $post->alias,
                'image'  => $post->image,
                'header' => $post->header,
                'text'   => $post->text
            ];
        }
        return $respPosts;
    }
}
