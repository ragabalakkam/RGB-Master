<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class ResetPasswordRequest extends BaseFormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'token' => [
        'nullable',
        'exists:password_resets,token'
      ],
      'newPassword' => [
        'required',
        'min:8'
      ],
      'confirmNewPassword' => [
        'required',
        'same:newPassword'
      ],
    ];
  }
}
