<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueExceptCurrent implements Rule
{
    private $model, $id, $invalid_locale;

    public function __construct($model, $id)
    {
        $this->model = $model;
        $this->id = $id;
    }

    public function passes($attribute, $value)
    {
        if ($attribute == 'name' && is_array($value))
        {
            $query = isset($this->id) ? $this->model::where('id', '!=', $this->id) : $this->model::all();
            foreach($value as $key => $v) {
                if($query->where('name', 'like', '%' . json_encode($v) . '%')->count()) {
                    $this->invalid_locale = $key;
                    return false;
                }
            }
            return true;
        }

        $results = $this->model::where($attribute, $value);
        if (isset($this->id)) $results->where('id', '!=', $this->id);
        return !!!$results->get()->count();        
    }
    
    public function message()
    {
        return 'unique' . (is_null($this->invalid_locale) ? '' : '.' . $this->invalid_locale);
    }
}
