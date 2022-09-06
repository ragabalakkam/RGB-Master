<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StartsWith implements Rule
{
    private $str;

    public function __construct($string)
    {
        $this->str = $string;
    }

    public function passes($attribute, $value)
    {
        return substr($value, 0, strlen($this->str)) === $this->str;
    }

    public function message()
    {
        return 'starts_with';
    }
}
