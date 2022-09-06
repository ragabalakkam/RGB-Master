<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class CheckExistsUsernameRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'value' => [
                'exists:users,username'
            ]
        ];
    }
}
