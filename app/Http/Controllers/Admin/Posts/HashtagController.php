<?php

namespace App\Http\Controllers\Admin\Posts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HashtagController extends PostsController
{
    protected function edit()
    {
        return response()
            -> view('admin.posts.hashtag.edit');
    }

    protected function attach()
    {
        return response()
            -> view('admin.posts.hashtag.attach');
    }

    protected function remove()
    {
        return response()
            -> view('admin.posts.hashtag.remove');
    }
}
