<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class ChangePasswordRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'newPassword' => [
        'required',
        'min:8'
      ],
      'confirmNewPassword' => [
        'required',
        'same:newPassword'
      ]
    ];
  }
}
