<?php

namespace App\Http\Requests\AdminSide\SubCategoryRequest;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Requests\AdminFormRequest;

class EditSubcategorySaveRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('subcategoryNames', json_decode($this->input()['subcategoryNames'], true));
        // clean alias from unnecessary symbols
        $this->request->set('newAlias', Helpers::cleanToOnlyLettersNumbers($this->input()['newAlias']));

        return parent::getValidatorInstance();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => self::REQUIRE_EXISTS['subcategories']['id'],
            'newCategoryId' => self::REQUIRE_EXISTS['categories']['id'],
            'newAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'subcategoryNames.*.name' => self::SUBCATEGORY_COMMON_RULES['name'],
            'subcategoryNames.*.id' => self::REQUIRE_EXISTS['subcategories_locale']['id']
        ];
    }
}
