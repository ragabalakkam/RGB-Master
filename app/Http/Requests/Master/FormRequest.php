<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\BaseFormRequest;
use App\Rules\LocalizedName;
use App\Rules\NotEmptyArray;

class FormRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
            ],
            'icon' => [
                'nullable',
            ],
            'order' => [
                'required',
                'integer',
            ],
            'dir' => [
                'nullable',
                'in:auto,ltr,rtl',
            ],
            'width' => [
                'nullable',
                'string',
            ],
            'cols' => [
                'integer',
            ],
            'class' => [
                'nullable',
                'string',
            ],
            'style' => [
                'nullable',
                'string',
            ],
            'fields' => [
                'required',
                'array',
                new NotEmptyArray,
            ],
            'active' => [
                'required',
                'boolean',
            ],
        ];
    }
}
