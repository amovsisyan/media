<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class Validation extends Controller
{
    public static function validateCategoryCreate($allRequest)
    {
        $rules = [
            'category_name' => 'required|min:2|max:30',
            'category_alias' => 'required|min:2|max:30',
        ];
        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validateSubcategoryCreate($allRequest)
    {
        $rules = [
            'subcategory_name' => 'required|min:2|max:30',
            'subcategory_alias' => 'required|min:2|max:30',
            'categorySelect' => 'required|integer',
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

    public static function validateEditCategorySearchValues($allRequest) {
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

    public static function validateEditCategorySearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required',
            'newAlias' => 'required|min:2|max:30',
            'newName' => 'required|min:2|max:30',
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return [
            'error' => false,
        ];
    }

    public static function validateEditSubcategorySearchValues($allRequest) {
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

    public static function validateEditSubcategorySearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required',
            'newCategoryId' => 'required|min:1|max:10',
            'newAlias' => 'required|min:2|max:30',
            'newName' => 'required|min:2|max:30',
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

    private static function _generateValidationErrorResponse($validator)
    {
        $errors = $validator->errors();
        $response = [];
        foreach ($errors->all() as $message) {
            $response[] = $message;
        }
        return [
            'error' => true,
            'type' => 'Validation Error',
            'response' => $response
        ];
    }
}
