<?php

namespace App\Http\Controllers\Admin\Posts;

use App\Hashtag;
use App\HashtagLocale;
use App\Http\Controllers\Admin\Response\ResponseController;
use App\Http\Controllers\Data\DBColumnLengthData;
use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Helpers\ResponsePrepareHelper;
use App\Http\Controllers\Services\Locale\LocaleSettings;
use App\Http\Requests\AdminSide\HashtagRequest\CreateHashtagRequest;
use App\Http\Requests\AdminSide\HashtagRequest\DeleteHashtagRequest;
use App\Http\Requests\AdminSide\HashtagRequest\EditHashtagSaveRequest;
use App\Http\Requests\AdminSide\HashtagRequest\EditHashtagSearchRequest;

class HashtagController extends PostsController
{
    const CATEGORYEDITSEARCHTYPES = [
        'byID' => '1',
        'byAlias' => '2'
    ];

    protected function editHashtag_get()
    {
        $response = Helpers::prepareAdminNavbars();

        return response()
            -> view('admin.posts.hashtag.edit', ['response' => $response]);
    }

    protected function editHashtag_post(EditHashtagSearchRequest $request)
    {
        $hashtag = self::_hashtagBySearchType($request);

        $searchResult = $hashtag->select('id', 'alias')
            ->with(['hashtagsLocale' => function ($query) {
                $query->select('id', 'hashtag', 'hashtag_id', 'locale_id');
            }])
            ->get();

        $response = ResponsePrepareHelper::PR_EditHashtag($searchResult);

        return response(
            [
                'error' => false,
                'response' => $response
            ]
        );
    }

    protected function editHashtagSave_post(EditHashtagSaveRequest $request)
    {
        try {
            $allRequest = $request->all();
            // Hashtag Main update
            $updateArr = [
                'alias' => $allRequest['hashtagAlias']
            ];
            $hashtaqg = Hashtag::updHashtagById($allRequest['id'], $updateArr);

            // Hashtag Locale update
            foreach ($allRequest['hashtagNames'] as $locale) {
                $localeUpdateArr = [
                    'hashtag' => $locale['name']
                ];
                HashtagLocale::updLocaleHashtagByID($locale['locale_id'], $localeUpdateArr);
            }
        } catch (\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected function editHashtagDelete_post(DeleteHashtagRequest $request)
    {
        try {
            Hashtag::delHashtagById($request->id);
        } catch(\Exception $e) {
            return ResponseController::_catchedResponse($e);
        }

        return response(['error' => false]);
    }

    protected function createHashtag_get()
    {
        $response = Helpers::prepareAdminNavbars();

        $response['colLength'] = DBColumnLengthData::getHashtagLenghts();
        $response['activeLocales'] = LocaleSettings::getActiveLocales();

        return response()
            -> view('admin.posts.hashtag.create', ['response' => $response]);
    }

    protected function createHashtag_post(CreateHashtagRequest $request)
    {
        try {
            $allRequest = $request->all();
            $createArr = [
                'alias' => $allRequest['hashtagAlias']
            ];
            $hashtag = Hashtag::create($createArr);

            $localeCreateArr = [];
            foreach ($allRequest['hashtagNames'] as $cat) {
                $localeCreateArr[] = [
                    'hashtag' => $cat['name'],
                    'locale_id' => $cat['locale_id']
                ];
            }

            $hashtagLocale = $hashtag->hashtagsLocale()
                ->createMany($localeCreateArr);
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
            case self::CATEGORYEDITSEARCHTYPES['byAlias']:
                return Hashtag::getHashtagsBuilderLikeAlias("%$request->searchText%");
                break;
            default:
                throw new \Exception('Can not find. Wrong hashtag type');
        }
    }
}
