<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

# Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RequestPasswordResetRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;

# Models
use App\Models\Auth\PasswordReset;

# Notifications
use App\Notifications\Auth\ResetPasswordRequestNotification;
use App\Notifications\Auth\PasswordResetSuccessNotification;

# added for password check
use Illuminate\Support\Facades\Hash;

# for time
use Carbon\Carbon;

use App\Models\User;

class ResetPasswordController extends Controller
{
  protected function requsetPasswordReset(RequestPasswordResetRequest $request)
  {
    $user = User::where('email', $request->email)->first();

    // delete all previous requests
    PasswordReset::where('user_id', $user->id)->delete();

    $resetReq = PasswordReset::create([
      'user_id' => $user->id,
      'token' => Str::random(100),
    ]);

    $user->notify(new ResetPasswordRequestNotification($resetReq->token));

    return response()->json(null, 200);
  }

  protected function checkResetPasswordToken($token)
  {
    if ($resetReq = PasswordReset::where('token', $token)->first()) {
      if (Carbon::parse(now())->diffInMinutes(Carbon::parse($resetReq->created_at)) >= 20) {
        PasswordReset::where('token', $token)->delete();
        return response()->json(null, 403);
      }

      return response()->json(true, 200);
    }

    return response()->json(null, 404);
  }

  protected function resetPassword(ResetPasswordRequest $request)
  {
    $resetReq = PasswordReset::where('token', $request->token)->first();

    $resetReq->user->update([
      'password' => bcrypt($request->confirmNewPassword),
    ]);

    $resetReq->user->notify(new PasswordResetSuccessNotification());

    response()->json('updated', 201);
  }

  public function changePassword(ChangePasswordRequest $request)
  {
    if (Hash::check($request->oldPassword, auth()->user()->password)) {

      auth()->user()->update([
        'password' => bcrypt($request->confirmNewPassword),
      ]);

      return response()->json(null, 204);
    }

    return response()->json(['errors' => ['oldPassword' => ['wrong']]], 422);
  }
}
