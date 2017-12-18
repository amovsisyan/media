<?php

namespace App\Http\Requests\AdminSide\PostRequest;

use App\Http\Requests\AdminFormRequest;

class CreatePostRequest extends AdminFormRequest
{
    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        // cause comes json, we need to change it to array to validate it
        $this->request->set('postHashtag', json_decode($this->input()['postHashtag'], true));
        $this->request->set('activeLocales', json_decode($this->input()['activeLocales'], true));

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
            // Top Main
            'postAlias' => self::POST_COMMON_RULES['alias'],
            'postSubcategory' => self::REQUIRE_EXISTS['subcategories']['id'],
            'postHashtag.*' => self::REQUIRE_EXISTS['hashtags']['id'],

            // Main
            'activeLocales.*' => 'required', // ru, en, am ...
            'header.*' => self::POST_COMMON_RULES['header'],
            'mainImage' => 'required|max:4000',
            'text.*' => self::POST_COMMON_RULES['text'],

            // Parts
            'partHeader.*.*' => self::POST_PARTS_COMMON_RULES['head'],
            'partImage.*.*' => 'required|max:4000',
            'partFooter.*.*' => self::POST_PARTS_COMMON_RULES['foot']
        ];
    }
}
