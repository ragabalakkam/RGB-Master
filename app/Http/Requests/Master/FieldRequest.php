<?php

namespace App\Http\Requests\Master;

use App\Http\Requests\BaseFormRequest;
use App\Rules\LocalizedName;

class FieldRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'form_id' => [
                'required',
                'exists:forms,id',
            ],
            'name' => [
                'required',
                new LocalizedName,
            ],
            'datatype' => [
                'required',
                'in:string,integer,float,boolean,timestamp,array,email,date',
            ],
            'icon' => [
                'nullable',
            ],
            'order' => [
                'integer',
            ],
            'dir' => [
                'nullable',
                'in:auto,ltr,rtl',
            ],
            'active' => [
                'required',
                'boolean',
            ],
            'required' => [
                'required',
                'boolean',
            ],
        ];
    }
}
