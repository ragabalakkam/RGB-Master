<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use App\Rules\StartsWith;

class CheckUniquePhoneRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'value' => [
        'required',
        'unique:users,phone',
        'digits:12',
        new StartsWith('966'),
      ],
    ];
  }
}
