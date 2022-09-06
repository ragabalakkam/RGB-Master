<?php

namespace App\Http\Requests\Versions;

use App\Http\Requests\BaseFormRequest;

class VersionRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'description'   => castNull($this->description),
            'notes'         => castNull($this->notes),
        ]);
    }

    public function rules()
    {
        return [
            'file' => [
                'required',
            ],
        ];
    }
}
