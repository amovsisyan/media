<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Hashtag;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validation;
use Illuminate\Http\Request;

class HashtagController extends PostsController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byName' => '2',
        'byAlias' => '3',
    ];

    protected function editHashtag_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        return response()
            -> view('admin.posts.hashtag.edit', ['response' => $response]);
    }

    protected function editHashtag_post(Request $request)
    {
        $validationResult = Validation::validateEditHashtagSearchValues($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                $hashtag = Hashtag::where('id', $request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                $hashtag = Hashtag::where('hashtag', 'like', "%$request->searchText%");
                break;
            default:
                $hashtag = Hashtag::where('alias', 'like', "%$request->searchText%");
        }
        $searchResult = $hashtag->select('id', 'hashtag', 'alias')->get();
        $response['hashtags'] = [];
        if (!empty($searchResult)) {
            foreach ($searchResult as $item) {
                $response['hashtags'][] = [
                    'id' => $item->id,
                    'alias' => $item->alias,
                    'name' => $item->hashtag
                ];
            }
        }
        return response(
            [
                'error' => false,
                'response' => $response
            ]
        );
    }

    protected function editHashtagSave_post(Request $request)
    {
        $validationResult = Validation::validateEditHashtagSearchValuesSave($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        try {
            Hashtag::where('id', $request->id)
                ->update([
                    'hashtag' => $request->newName,
                    'alias' => $request->newAlias
                ]);
        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        return response(['error' => false]);
    }

    protected function attachHashtag()
    {
        return response()
            -> view('admin.posts.hashtag.attach');
    }

    protected function editHashtagDelete_post(Request $request)
    {
        $validationResult = Validation::validateHashtagDelete($request->all());
        if ($validationResult['error']) {
            return response(
                [
                    'error' => true,
                    'type' => $validationResult['type'],
                    'response' => $validationResult['response']
                ], 404
            );
        }

        try {
            Hashtag::where('id', $request->id)->delete();
        } catch(\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        return response(['error' => false]);
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

        try {
            $hashtag = Hashtag::create(
                [
                    'hashtag' => $request->hashtag_name,
                    'alias' => $request->hashtag_alias
                ]
            );
        } catch(\Exception $e) {
            return response(
                [
                    'error' => true,
                    'type' => 'Some Other Error',
                    'response' => [$e->getMessage()]
                ], 404
            );
        }

        if ($hashtag) {
            return response(['error' => false]);
        }
    }
}
