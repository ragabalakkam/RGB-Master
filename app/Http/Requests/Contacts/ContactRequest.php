<?php

namespace App\Http\Requests\Contacts;

use App\Http\Requests\BaseFormRequest;

class ContactRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
            'phone' => [
                'required',
            ],
            'email' => [
                'required',
            ],
            'message' => [
                'required',
            ],
        ];
    }
}
