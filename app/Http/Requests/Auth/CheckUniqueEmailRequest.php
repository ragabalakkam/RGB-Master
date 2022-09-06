<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class CheckUniqueEmailRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation()
  {
    $parts = explode('@', $this->value);
    $this->merge([
      'value' => strtolower(str_replace('.', '', ($parts[0] ?? '')) . '@' . ($parts[1] ?? '')),
    ]);
  }

  public function rules()
  {
    return [
      'value' => [
        'required',
        'email:rfc,dns',
        'unique:users,email'
      ]
    ];
  }
}
