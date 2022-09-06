<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class RegisterRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'username' => pick_username($this->name),
            'email' => normalize_email($this->email),
        ]);
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'min:8',
            ],
            'confirmPassword' => [
                'required',
                'same:password',
            ]
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'min.numeric',
            'confirmPassword.same' => 'samePasswords',
        ];
    }
}
