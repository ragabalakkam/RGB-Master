<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotEmptyArray implements Rule
{
    private $at_least;

    public function __construct($at_least = 1)
    {
        $this->at_least = $at_least;
    }

    public function passes($attribute, $value)
    {
        if (!is_array($value) || count($value) < $this->at_least)
            return false;

        return true;
    }

    public function message()
    {
        return 'emptyArray';
    }
}
