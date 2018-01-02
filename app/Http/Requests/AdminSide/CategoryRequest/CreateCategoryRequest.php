<?php

namespace App\Http\Requests\AdminSide\CategoryRequest;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Requests\AdminFormRequest;

class CreateCategoryRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('categories_names', json_decode($this->input()['categories_names'], true));
        // clean alias from unnecessary symbols
        $this->request->set('category_alias', Helpers::cleanToOnlyLettersNumbers($this->input()['category_alias']));

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
            'categories_names.*.locale_id' => self::REQUIRE_EXISTS['locale']['id'],
            'categories_names.*.name' => self::CATEGORY_COMMON_RULES['name'] . '|unique:categories_locale,name',
            'category_alias' => self::CATEGORY_COMMON_RULES['alias'] . '|unique:categories,alias'
        ];
    }
}
