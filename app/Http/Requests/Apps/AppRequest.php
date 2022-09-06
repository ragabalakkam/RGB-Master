<?php

namespace App\Http\Requests\Apps;

use App\Http\Requests\BaseFormRequest;

# rules
use App\Rules\LocalizedName;

class AppRequest extends BaseFormRequest
{
    public function prepareForValidation()
    {
        $this->merge([
            'name'                  => json_decode($this->name, true),
            'description'           => json_decode($this->description, true),
            'configuration_groups'  => json_decode($this->configuration_groups, true),
        ]);
    }
    
    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
            ],
            'description' => [
                'nullable',
                new LocalizedName,
            ],
        ];
    }
}
