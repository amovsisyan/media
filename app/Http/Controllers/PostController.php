<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Helpers\Helpers;


class PostController extends SubcategoryController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPost(Request $request, $category, $subcategory, $post)
    {
        $post = Helpers::getPostByRequest($post);
        if ($post) {
            $post_parts = $post->postParts()->get();
            $hashtags = $post->hashtags()->get();

            $response = [
                'navbar'      => $this->getNavbar(),
                'post_header' => $post->header,
                'post_parts'  => $post_parts,
                'hashtags'    => $hashtags
            ];
        }

        return response()
            -> view('current-post', ['response' => $response]);
    }
}
