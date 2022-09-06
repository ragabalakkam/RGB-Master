<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Http\Requests\BaseFormRequest;

# rules
use App\Rules\StartsWith;

class UserRequest extends BaseFormRequest
{
    
    public function authorize()
    {      
        return true;
    }
    public function rules()
    {
        return 
        [
            'name' => [
                'required',
                'string'
            ],
            'username' => [
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
                'numeric'
            ],
            'phone' => [
                'required',
                'digits:12',
                new StartsWith('966'),
            ],
        ];
    }
}
