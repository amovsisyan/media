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

    public static function validateSubcategoryCreate($allRequest)
    {
        $rules = [
            'subcategoryAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'categoryId' => 'required|integer'
        ];
        // todo standardizatoin needed  1-2 -->3
        foreach ($allRequest['subcategoryNames'] as $key => $value) {
            $idValidation = "subcategoryNames." . $key . '.locale_id';
            $nameValidation = "subcategoryNames." . $key . '.name';
            $rules[$idValidation] = 'required';
            $rules[$nameValidation] = self::SUBCATEGORY_COMMON_RULES['name'];
        };


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
            'id' => 'required|min:1|max:10',
            'newCategoryId' => 'required|min:1|max:10',
            'newAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
        ];

        foreach ($allRequest['subcategoryNames'] as $key => $name) {
            $idValidation = "subcategoryNames." . $key . '.id';
            $nameValidation = "subcategoryNames." . $key . '.name';
            $rules[$idValidation] = 'required|min:1|max:10';
            $rules[$nameValidation] = self::SUBCATEGORY_COMMON_RULES['name'];
        }

        $validator = Validator::make($allRequest, $rules);

        if ($validator->fails()) {
            return self::_generateValidationErrorResponse($validator);
        };

        return self::_generateValidationSimpleOKResponse();
    }
}
