<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

# Models
use App\Models\User;
use App\Models\Auth\EmailVerification;

# Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\VerifyEmailRequest;

# Notifications
use App\Notifications\Auth\EmailVerifiedNotification;
use App\Notifications\Auth\WelcomeNotification;


class VerifyEmailController extends Controller
{
  public function store(Request $request)
  {
    $request->merge(['token' => Str::random(50)]);
    $verification = EmailVerification::create($request->all());
    User::find($request->user_id)->notify(new WelcomeNotification($verification->token));
    return $verification;
  }

  protected function verifyEmail(VerifyEmailRequest $request)
  {
    $verification = EmailVerification::where('token', $request->token)->first();

    // email already verified
    if ($verification->user->email_verified_at) {
      EmailVerification::where('token', $verification->token)->delete();
      return response()->json(['errors' => ['token' => ['verified']]], 422);
    }

    $verification->user->update(['email_verified_at' => now()]);

    $verification->user->notify(new EmailVerifiedNotification());

    EmailVerification::where('token', $request->token)->delete();

    return response()->json(null, 200);
  }

  protected function resendVerificationLink()
  {
    $user = auth()->user();

    EmailVerification::where('user_id', $user->id)->delete();

    $this->store(new Request(['user_id' => $user->id]));

    return response()->json(null, 200);
  }
}
