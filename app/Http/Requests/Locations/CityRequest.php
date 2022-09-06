<?php

namespace App\Http\Requests\Locations;

use App\Http\Requests\BaseFormRequest;

use App\Rules\LocalizedName;

class CityRequest extends BaseFormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'state_id' => [
                'required',
                'exists:states,id',
            ],
            'name' => [
                'required',
                new LocalizedName,
            ],
        ];
    }
}
