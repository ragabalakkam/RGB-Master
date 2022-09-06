<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use App\Rules\StartsWith;

class VerifyPhoneRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => [
                'required',
                'exists:phone_verifications,token'
            ],
            'phone' => [
                'required',
                'exists:phone_verifications,phone',
                'digits:12',
                new StartsWith('966'),
            ],
        ];
    }
}
