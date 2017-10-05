<?php

namespace App\Http\Controllers\Helpers\Validator;

use Validator;

class PostsValidation extends AbstractValidator
{
    public static function createPostMainFieldsValidations($allRequest) {
        $rules = [
            'postAlias' => 'required|min:2|max:60',
            'postMainHeader' => 'required|min:2|max:60',
            'postMainText' => 'required|min:2|max:60',
            'postMainImage' => 'required|image ',
            'postSubcategory' => 'required',
            'postHashtag' => 'required',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function createPostPartFieldsValidations($allRequest) {
        $rules = [];

        foreach ($allRequest['partHeader'] as $key => $value) {
            $k = 'partHeader.' . $key;
            $rules[$k] = 'required|max:300';
        };

        foreach ($allRequest['partImage'] as $key => $value) {
            $k = 'partImage.' . $key;
            $rules[$k] = 'required|image';
        };

        foreach ($allRequest['partFooter'] as $key => $value) {
            $k = 'partFooter.' . $key;
            $rules[$k] = 'required|max:300';
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function updatePostMainFieldsValidations($allRequest) {
        $rules = [
            'postAlias' => 'required|min:2|max:60',
            'postMainHeader' => 'required|min:2|max:60',
            'postMainText' => 'required|min:2|max:60',
            'postSubcategory' => 'required',
            'postHashtag' => 'required',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function updatePostPartFieldsValidations($allRequest) {
        $rules = [];

        foreach ($allRequest['partHeader'] as $key => $value) {
            $k = 'partHeader.' . $key;
            $rules[$k] = 'required|max:300';
        };

        foreach ($allRequest['partFooter'] as $key => $value) {
            $k = 'partFooter.' . $key;
            $rules[$k] = 'required|max:300';
        };

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validatePostPartsUpdate($allRequest) {
        $rules = [
            'partId' => 'required|min:1|max:10',
            'head' => 'required|min:1|max:300',
            'foot' => 'required|min:1|max:300',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validatePostPartDelete($allRequest) {
        $rules = [
            'partId' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validatePostDelete($allRequest) {
        $rules = [
            'postId' => 'required|min:1|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
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

        return [
            'error' => false,
        ];
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

        return [
            'error' => false,
        ];
    }

    public static function validateEditHashtagSearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required',
            'newAlias' => 'required|min:2|max:40',
            'newName' => 'required|min:2|max:40',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validateHashtagCreate($allRequest) {
        $rules = [
            'hashtag_name' => 'required|min:2|max:40',
            'hashtag_alias' => 'required|min:2|max:40',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validateHashtagDelete($allRequest) {
        $rules = [
            'id' => 'required|max:10',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }
}
