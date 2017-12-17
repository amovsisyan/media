<?php

namespace App\Http\Requests\AdminSide\SubCategoryRequest;

use App\Http\Requests\AdminFormRequest;

class DeleteSubcategoryRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
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
            'subcategoryId' => 'required|exists:subcategories,id'
        ];
    }
}
