<?php

namespace App\Http\Requests\Locations;

use App\Http\Requests\BaseFormRequest;

use App\Rules\LocalizedName;

class DistrictRequest extends BaseFormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'city_id' => [
                'required',
                'exists:cities,id',
            ],
            'name' => [
                'required',
                new LocalizedName,
            ],
            'neCoordinates' => [
                'nullable',
                'string',
            ],
            'swCoordinates' => [
                'nullable',
                'string',
            ],

        ];
    }
}
