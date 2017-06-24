<?php

namespace App\Http\Controllers;

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
        //TODO need function helper which will get parametr will return Id
        $expl_post = explode('_', $post);
        $post_id = $expl_post[count($expl_post)-1];

        $post = Post::findOrFail($post_id);

        $post_parts = $post->postParts()->get();
        $hashtags = $post->hashtags()->get();

        $response = [
            'navbar'      => $this->getNavbar(),
            'post_header' => $post->header,
            'post_parts'  => $post_parts,
            'hashtags'    => $hashtags
        ];

        return response()
            -> view('current-post', ['response' => $response]);
    }
}
