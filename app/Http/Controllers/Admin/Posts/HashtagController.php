<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Hashtag;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\Validator\PostsValidation;
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
        $validationResult = PostsValidation::validateEditHashtagSearchValues($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        $hashtag = self::_hashtagBySearchType($request);

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
        $validationResult = PostsValidation::validateEditHashtagSearchValuesSave($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $updateArr = [
                'hashtag' => $request->newName,
                'alias' => $request->newAlias
            ];
            Hashtag::updHashtagById($request->id, $updateArr);
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
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
        $validationResult = PostsValidation::validateHashtagDelete($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            Hashtag::delHashtagById($request->id);
        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected function createHashtag_get()
    {
        $response = Helpers::prepareAdminNavbars(request()->segment(3));

        $response['colLength'] = DBColumnLengthData::HASHTAG_TABLE;

        return response()
            -> view('admin.posts.hashtag.create', ['response' => $response]);
    }

    protected function createHashtag_post(Request $request)
    {
        $validationResult = PostsValidation::validateHashtagCreate($request->all());

        if ($validationResult['error']) {
            return ResponseController::_validationResultResponse($validationResult);
        }

        try {
            $createArr = [
                'hashtag' => $request->hashtag_name,
                'alias' => $request->hashtag_alias
            ];
            Hashtag::create($createArr);
        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected static function _hashtagBySearchType($request) {
        switch ($request->searchType) {
            case self::CATEGORYEDITSEARCHTYPES['byID']:
                return Hashtag::getHashtagBuilderByID($request->searchText);
                break;
            case self::CATEGORYEDITSEARCHTYPES['byName']:
                return Hashtag::getHashtagsBuilderLikeHashtag("%$request->searchText%");
                break;
            case self::CATEGORYEDITSEARCHTYPES['byAlias']:
                return Hashtag::getHashtagsBuilderLikeAlias("%$request->searchText%");
                break;
            default:
                throw new \Exception('Can not find. Wrong hashtag type');
        }
    }
}
