<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Hashtag;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;

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
        $response = $this->prepareNavbars(request()->segment(3));

        return response()
            -> view('admin.posts.hashtag.create', ['response' => $response]);
    }

    protected function createHashtag_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hashtag_name' => 'required|min:2|max:40',
            'hashtag_alias' => 'required|min:2|max:40',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response = [];
            foreach ($errors->all() as $message) {
                $response[] = $message;
            }
            return response(
                [
                    'error' => true,
                    'validate_error' => true,
                    'response' => $response
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

    // Prepare Left and Panel Navbar response
    // Must be like helper as for now it also the same logic used in SubCategories
    // think will be used everywhere,
    // ToDo Should here add CACHE part logic
    protected function prepareNavbars($part)
    {
        $response = [];
        $adminController = new AdminController();
        $response['leftNav'] = $adminController->getLeftNavbar();
        $response['panel'] = $adminController->getPanelNavbar($part);
        return $response;
    }
}
