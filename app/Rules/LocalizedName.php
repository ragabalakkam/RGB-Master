<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Lang\Locale;

class LocalizedName implements Rule
{
    public function passes($attribute, $value)
    {
        if (is_string($value)) return true;

        foreach (Locale::all() as $locale) {
            if (!$value[$locale->label]) return false;
        }

        return true;
    }

    public function message()
    {
        return 'required';
    }
}
