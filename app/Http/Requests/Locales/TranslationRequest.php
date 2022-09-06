<?php

namespace App\Http\Requests\Locales;

use App\Http\Requests\BaseFormRequest;

class TranslationRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'key' => [
            //     'required',
            //     'unique:translations,key'
            // ],
        ];
    }
}
