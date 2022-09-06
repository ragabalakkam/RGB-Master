<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => [
                'required',
                'string',
                // 'exists:users,username',
            ],
            'password' => [
                'required',
                'string',
                'min:8'
            ]
        ];
    }
}
