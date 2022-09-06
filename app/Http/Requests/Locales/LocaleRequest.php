<?php

namespace App\Http\Requests\Locales;

use App\Http\Requests\BaseFormRequest;

# modelse
use App\Models\Lang\Locale;

# rules
use App\Rules\LocalizedName;
use App\Rules\UniqueExceptCurrent;

class LocaleRequest extends BaseFormRequest
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
                new UniqueExceptCurrent(Locale::class, $this->id),    
            ],
            'label' => [
                'required',
                'string',
            ],
            'dir' => [
                'required',
                'in:rtl,ltr',
            ],
        ];
    }
}
