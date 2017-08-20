<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Hashtag;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use Illuminate\Http\Request;

class HashtagController extends PostsController
{
    protected function editHashtag()
    {
        return response()
            -> view('admin.posts.hashtag.edit');
    }

    protected function attachHashtag()
    {
        return response()
            -> view('admin.posts.hashtag.attach');
    }

    protected function deleteHashtag()
    {
        return response()
            -> view('admin.posts.hashtag.delete');
    }

    protected function createHashtag_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.posts.hashtag.create', ['response' => $response]);
    }

    protected function createHashtag_post(Request $request)
    {
        $validationResult = Validation::validateHashtagCreate($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        $hashtag = Hashtag::create(
            [
                'hashtag' => $request->hashtag_name,
                'alias' => $request->hashtag_alias
            ]
        );

        if ($hashtag) {
            return response(['error' => false]);
        }

        return response(
            [
                'error' => true,
            ], 404
        );
    }
}
