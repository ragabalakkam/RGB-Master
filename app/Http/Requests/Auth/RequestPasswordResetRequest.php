<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class RequestPasswordResetRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $this->merge([
      'email' => strtolower(str_replace('.', '', explode('@', $this->email)[0]) . '@' . explode('@', $this->email)[1]),
    ]);
  }

  public function rules()
  {
    return [
      'email' => [
        'required',
        'exists:users,email'
      ]
    ];
  }
}
