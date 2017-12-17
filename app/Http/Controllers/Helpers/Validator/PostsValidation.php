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
        'head' => 'required|min:2|max:' . DBColumnLengthData::POST_PARTS_TABLE['head'],
        'foot' => 'required|min:2|max:' . DBColumnLengthData::POST_PARTS_TABLE['foot']
    ];

    public static function createPostMainFieldsValidations($allRequest) {

        $rules = [
            'postAlias' => self::POST_COMMON_RULES['alias'],
            'postSubcategory' => 'required',
            'postHashtag' => 'required'
        ];

        foreach ($allRequest['activeLocales'] as $activeLocale) {
            $rules['header.' . $activeLocale] = self::POST_COMMON_RULES['header'];
            $rules['mainImage.' . $activeLocale] = 'required|image ';
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
            'postSubcategory' => 'required',
            'postHashtag' => 'required'
        ];

        foreach ($allRequest['header'] as $locale => $value) {
            $rules['header.' . $locale] = self::POST_COMMON_RULES['header'];
            $rules['text.' . $locale] = self::POST_COMMON_RULES['text'];
        }

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
            'searchType' => 'required|integer',
            'searchText' => 'required',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    // todo need to remove
//    public static function postPartsAttachSave($allRequest) {
//        $rules = [
//            'newPostId' => 'required|min:1|max:10',
//        ];
//
//        $validator = Validator::make($allRequest, $rules);
//
//        if ($validator->fails()) {
//            return self::_generateValidationErrorResponse($validator);
//        };
//
//        return self::_generateValidationSimpleOKResponse();
//    }
}
