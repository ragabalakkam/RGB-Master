<?php

namespace App\Http\Requests\Locations;

use App\Http\Requests\BaseFormRequest;

# models
use App\Models\Locations\State;

# rules
use App\Rules\LocalizedName;
use App\Rules\NotEmptyArray;
use App\Rules\UniqueExceptCurrent;

class StateRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'country_id' => [
                'required',
                'exists:countries,id',
            ],
            'name' => [
                'required',
                new LocalizedName,
                new UniqueExceptCurrent(State::class, $this->id),
            ],
        ];
    }
}
