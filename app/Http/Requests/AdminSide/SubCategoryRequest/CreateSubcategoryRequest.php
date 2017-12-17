<?php

namespace App\Http\Requests\AdminSide\SubCategoryRequest;

use App\Http\Requests\AdminFormRequest;

class CreateSubcategoryRequest extends AdminFormRequest
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
            'categoryId' => 'required|exists:categories,id',
            'subcategoryAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'subcategoryNames.*.locale_id' => 'required|exists:locale,id',
            'subcategoryNames.*.name' => self::SUBCATEGORY_COMMON_RULES['name']
        ];
    }
}
