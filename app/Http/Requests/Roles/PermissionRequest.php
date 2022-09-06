<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\BaseFormRequest;

class PermissionRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}
