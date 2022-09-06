<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseFormRequest;

class UpdateUserImageRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'image' => [
                'required',
                'mimes:jpeg,jpg,png,gif',
                // 'max:10000',
            ],
        ];
    }
}
