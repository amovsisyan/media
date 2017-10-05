<?php

namespace App\Http\Controllers\Helpers\Validator;

use Validator;

class CategoriesValidation extends AbstractValidator
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
}
