<?php

namespace App\Http\Requests\Roles;

use App\Http\Requests\BaseFormRequest;
use App\Rules\LocalizedName;

class RoleRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
            ],
            'permission_ids' => [
                'array',
                'required',
            ],
        ];
    }
}
