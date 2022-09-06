<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class CheckUniqueUsernameRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'value' => [
        'unique:users,username'
      ]
    ];
  }
}
