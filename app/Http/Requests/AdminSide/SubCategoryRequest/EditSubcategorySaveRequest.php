<?php

namespace App\Http\Requests\AdminSide\SubCategoryRequest;

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
            'id' => 'required|max:10|exists:subcategories,id',
            'newCategoryId' => 'required|max:10|exists:categories,id',
            'newAlias' => self::SUBCATEGORY_COMMON_RULES['alias'],
            'subcategoryNames.*.name' => self::SUBCATEGORY_COMMON_RULES['name'],
            'subcategoryNames.*.id' => 'required|max:10|exists:subcategories_locale,id'
        ];
    }
}
