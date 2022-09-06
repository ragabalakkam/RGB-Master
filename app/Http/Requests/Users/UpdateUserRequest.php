<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseFormRequest;

# rules
use App\Rules\StartsWith;

class UpdateUserRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'username' => normalize_username($this->username),
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
            'username' => [
                'required',
                'string',
                'unique:users,username,' . auth()->user()->id,
            ],
            'email' => [
                'nullable',
                'email:rfc,dns',
                'unique:users,email,' . auth()->user()->id,
            ],
            'phone' => [
                'nullable',
                'numeric',
                'digits:12',
                new StartsWith('966'),
            ],
        ];
    }
}
