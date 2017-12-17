<?php

namespace App\Http\Requests\AdminSide\CategoryRequest;

use App\Http\Requests\AdminFormRequest;

class DeleteCategoryRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it

        $this->request->set('data', json_decode($this->input()['data'], true));

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
            'data.*' => 'required|exists:categories,id'
        ];
    }
}
