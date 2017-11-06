<?php

namespace App\Http\Controllers\Helpers\Validator;

use App\Http\Controllers\Data\DBColumnLengthData;
use Validator;

class CategoriesValidation extends AbstractValidator
{
    const CATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::CATEGORIES_LOCALE_TABLE['name']
    ];

    const SUBCATEGORY_COMMON_RULES = [
        'alias' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_TABLE['alias'],
        'name' => 'required|min:2|max:' . DBColumnLengthData::SUBCATEGORIES_LOCAL_TABLE['name']
    ];

    public static function validateCategoryCreate($allRequest)
    {
        // todo bad validation, very bad
        foreach ($allRequest['categories_names'] as $key => $value) {
            $idValidation = "categories_names." . $key;
            $rules[$idValidation] = 'required';
        };
        $rules['category_alias'] = self::CATEGORY_COMMON_RULES['alias'];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditCategorySearchValues($allRequest)
    {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditCategorySearchValuesSave($allRequest)
    {
        $rules = [
            'id' => 'required',
            'newAlias' => self::CATEGORY_COMMON_RULES['alias'],
            'newName' => self::CATEGORY_COMMON_RULES['name']
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateSubcategoryCreate($allRequest)
    {
        $rules = [
            'subcategory_alias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'subcategory_name' => self::SUBCATEGORY_COMMON_RULES['name'],
            'categorySelect' => 'required|integer'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateSubcategoryDelete($allRequest)
    {
        $rules = [
            'subcategoryId' => 'required|min:1|max:10'
        ];
        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditSubcategorySearchValues($allRequest) {
        $rules = [
            'searchType' => 'required',
            'searchText' => 'required'
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }

    public static function validateEditSubcategorySearchValuesSave($allRequest) {
        $rules = [
            'id' => 'required',
            'newCategoryId' => 'required|min:1|max:10',
            'newAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'newName' => self::SUBCATEGORY_COMMON_RULES['name']
        ];

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }
}
