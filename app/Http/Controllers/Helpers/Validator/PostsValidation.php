<?php

namespace App\Http\Controllers\Helpers\Validator;

use App\Http\Controllers\Data\DBColumnLengthData;
use Validator;

class PostsValidation extends AbstractValidator
{
    const POST_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::POSTS_TABLE['alias'],
        'header' => 'required|min:2|max:' . DBColumnLengthData::POSTS_LOCALE_TABLE['header'],
        'text' => 'required|min:2|max:' . DBColumnLengthData::POSTS_LOCALE_TABLE['text']
    ];

    const POST_PARTS_COMMON_RULES = [
        'head' => 'required|max:' . DBColumnLengthData::POST_PARTS_TABLE['head'],
        'foot' => 'required|max:' . DBColumnLengthData::POST_PARTS_TABLE['foot']
    ];

    const HASHTAG_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::HASHTAG_TABLE['alias'],
        'hashtag' => 'required|min:2|max:' . DBColumnLengthData::HASHTAG_LOCALE_TABLE['hashtag']
    ];

    public static function createPostMainFieldsValidations($allRequest) {

        $rules = [
            'postAlias' => self::POST_COMMON_RULES['alias'],
            'postSubcategory' => 'required',
            'postHashtag' => 'required'
        ];

        foreach ($allRequest['activeLocales'] as $activeLocale) {
            $rules['header.' . $activeLocale] = self::POST_COMMON_RULES['header'];
            $rules['image.' . $activeLocale] = 'required|image ';
            $rules['text.' . $activeLocale] = self::POST_COMMON_RULES['text'];
        }

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function createPostPartFieldsValidations($allRequest) {
        $rules = [];
        foreach ($allRequest['partHeader'] as $locale => $partHeaderLocaled) {
            foreach ($partHeaderLocaled as $key => $partHeader) {
                $keyHeader = 'partHeader.' . $locale . '.' . $key;
                $rules[$keyHeader] = self::POST_PARTS_COMMON_RULES['head'];
            }
        }

        foreach ($allRequest['partImage'] as $locale => $partImageLocaled) {
            foreach ($partImageLocaled as $key => $partImage) {
                $keyImage = 'partImage.' . $locale . '.' . $key;
                $rules[$keyImage] = 'required|image';
            }
        }

        foreach ($allRequest['partFooter'] as $locale => $partFooterLocaled) {
            foreach ($partFooterLocaled as $key => $partFooter) {
                $keyFooter = 'partFooter.' . $locale . '.' . $key;
                $rules[$keyFooter] = self::POST_PARTS_COMMON_RULES['foot'];
            }
        }

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function updatePostMainFieldsValidations($allRequest) {
        $rules = [
            'postAlias' => self::POST_COMMON_RULES['alias'],
            'postMainHeader' => self::POST_COMMON_RULES['header'],
            'postMainText' => self::POST_COMMON_RULES['text'],
            'postSubcategory' => 'required',
            'postHashtag' => 'required'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function updatePostPartFieldsValidations($allRequest) {
        $rules = [];

        foreach ($allRequest['partHeader'] as $key => $value) {
            $k = 'partHeader.' . $key;
            $rules[$k] = self::POST_PARTS_COMMON_RULES['head'];
        };

        foreach ($allRequest['partFooter'] as $key => $value) {
            $k = 'partFooter.' . $key;
            $rules[$k] = self::POST_PARTS_COMMON_RULES['foot'];
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validatePostPartsUpdate($allRequest) {
        $rules = [
            'partId' => 'required|min:1|max:10',
            'head' => self::POST_PARTS_COMMON_RULES['head'],
            'foot' => self::POST_PARTS_COMMON_RULES['foot']
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validatePostPartDelete($allRequest) {
        $rules = [
            'partId' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validatePostDelete($allRequest) {
        $rules = [
            'postId' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditPostSearchValues($allRequest) {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditHashtagSearchValues($allRequest) {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditHashtagSearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required|integer',
            'hashtagAlias' => self::HASHTAG_COMMON_RULES['alias']
        ];
        // todo standardizatoin needed  1-2 -->5
        foreach ($allRequest['hashtagNames'] as $key => $value) {
            $idValidation = "hashtagNames." . $key . '.locale_id';
            $nameValidation = "hashtagNames." . $key . '.name';
            $rules[$idValidation] = 'required|integer';
            $rules[$nameValidation] = self::HASHTAG_COMMON_RULES['hashtag'];
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateHashtagCreate($allRequest) {

        $rules = [
            'hashtagAlias' => self::HASHTAG_COMMON_RULES['alias']
        ];

        // todo standardizatoin needed  1-2 -->3
        foreach ($allRequest['hashtagNames'] as $key => $value) {
            $idValidation = "hashtagNames." . $key . '.locale_id';
            $nameValidation = "hashtagNames." . $key . '.name';
            $rules[$idValidation] = 'required';
            $rules[$nameValidation] = self::HASHTAG_COMMON_RULES['hashtag'];
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateHashtagDelete($allRequest) {
        $rules = [
            'id' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function postPartsAttachSave($allRequest) {
        $rules = [
            'newPostId' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }
}
