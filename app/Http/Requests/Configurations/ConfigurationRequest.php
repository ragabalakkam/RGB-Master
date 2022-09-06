<?php

namespace App\Http\Requests\Configurations;

use App\Http\Requests\BaseFormRequest;

class ConfigurationRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'key' => [
                'required',
                // 'unique:configurations,key,except,id',
                'string',
            ],
            'value' => [
                'required',
            ],
        ];
    }
}
