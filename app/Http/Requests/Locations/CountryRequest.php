<?php

namespace App\Http\Requests\Locations;

use App\Http\Requests\BaseFormRequest;

# models
use App\Models\Locations\Country;

# rules
use App\Rules\LocalizedName;
use App\Rules\UniqueExceptCurrent;

class CountryRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                new LocalizedName,
                new UniqueExceptCurrent(Country::class, $this->id),
            ],
        ];
    }
}
