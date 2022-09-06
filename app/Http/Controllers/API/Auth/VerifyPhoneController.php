<?php

namespace App\Http\Controllers\API\Auth;

# Helpers
use Illuminate\Support\Str;

# Models
use App\Models\Auth\PhoneVerification;

# Requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\VerifyPhoneRequest;

# Controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;

class VerifyPhoneController extends Controller
{
  public function store(Request $request)
  {
    if (!hasModule('smsServiceUser'))
      return response()->json();

    PhoneVerification::where('phone', $request->phone)->delete();

    $request->merge(['token' => rand_digits(5)]);
    $verification = PhoneVerification::create($request->all());
    $token = $verification->token;

    if (config('app.demo'))
      return response()->json($token);

    $name = getGeneralInformation('name');
    $name = is_string($name) ? json_decode($name) : (is_array($name) ? json_decode(json_encode($name)) : $name);
    // $message = "Welcome to " . capitalize($name->en) . " ! Your verification code is $token";
    $message = "مرحباً بك في " . $name->ar . " ! رمز التفعيل هو $token";

    $response = SmsController::send($message, [$request->phone]);
    return response()->json($response);
  }

  public function verify(VerifyPhoneRequest $request)
  {
    $verification = PhoneVerification::where(['phone' => $request->phone, 'token' => $request->token])->first();
      
    if (!hasModule('smsServiceUser')) {
      $verification->update(['verified_at' => now()]);
      return response()->json();
    }

    if (is_null($verification))
      return response()->json(['errors' => ['token' => ['invalid']]], 422);

    # phone already verified
    if ($verification->_verified_at)
      return response()->json();

    $verification->update(['verified_at' => now()]);

    return response()->json(null, 200);
  }
}
