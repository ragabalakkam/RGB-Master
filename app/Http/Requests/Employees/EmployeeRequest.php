<?php

namespace App\Http\Requests\Employees;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use App\Rules\StartsWith;
use App\Rules\UniqueExceptCurrent;

class EmployeeRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'email'     => normalize_email($this->email),
            'username'  => pick_username($this->name),
        ]);
    }

    public function rules()
    {
        $validation =  [
            "name" => [
                "required",
                "string",
            ],
            "email" => [
                "nullable",
                "email:rfc,dns",
                new UniqueExceptCurrent(User::class, $this->id),
            ],
            "phone" => [
                "nullable",
                'digits:12',
                new StartsWith('966'),
                new UniqueExceptCurrent(User::class, $this->id),
            ],
            'department_id' => [
                'nullable',
                'numeric',
                'exists:departments,id'
            ],
            'password' => [
                'nullable',
                'min:8',
            ],
            'confirmPassword' => [
                'nullable',
                'same:password',
            ],
            'role_ids' => [
                'nullable',
                'array',
            ],
        ];

        return $validation;
    }

    public function messages()
    {
        return [
            'user_id.required_if'   => 'required',
            'name.required_if'      => 'required',
            'phone.required_if'     => 'required',
            'email.required_if'     => 'required',
            'password.min'          => 'min.numeric',
        ];
    }
}
