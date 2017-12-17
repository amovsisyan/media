<?php

namespace App\Http\Requests\AdminSide\SubCategoryRequest;

use App\Http\Requests\AdminFormRequest;

class EditSubcategorySearchRequest extends AdminFormRequest
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
            'searchType' => 'required',
            'searchText' => 'required'
        ];
    }
}
