<?php

namespace App\Http\Requests\Versions;

use App\Http\Requests\BaseFormRequest;

class UpdateFileRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'in:vendor,node_modules',
            ],
            'file' => [
                'required',
            ],
        ];
    }
}
