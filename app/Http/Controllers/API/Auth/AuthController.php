<?php

namespace App\Http\Controllers\API\Auth;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

# models
use App\Models\User;
use App\Models\Auth\PhoneVerification;

# facades
use Illuminate\Support\Str;

class AuthController extends Controller
{
  public function register(RegisterRequest $request, $return_cookies = true)
  {
    # create user
    $user = User::create(array_merge($request->all(), [
      'username' => pick_username($request->username ?? $request->name),
      'password' => bcrypt($request->password),
    ]));

    # verify email
    if (false) app(VerifyEmailController::class)->store(new Request(['user_id' => $user->id]));
    else $user->update(['email_verified_at' => now()]);

    # verify phone
    if (true || (isset($request->phoneVerificationCode) && (PhoneVerification::where('token', $request->phoneVerificationCode)->first()->verified_at ?? null))) {
      $user->update(['phone_verified_at' => now()]);
    }

    if (!$return_cookies)
      return response()->json($user);

    $token = auth()->login($user);
    return $this->respondWithToken($token);
  }

  public function login(LoginRequest $request)
  {
    $login_methods = [
      'username',
      'phone',
      'email',
    ];

    foreach ($login_methods as $key => $method) {

      if ($method == 'email')
        $request->merge(['username' => normalize_email($request->username)]);

      $user = User::where($method, '!=', null)->where($method, $request->username)->first() ?? null;

      if ($username = $user->username ?? null) {

        $login_methods[$key] = true;

        if ($token = auth('api')->attempt(['username' => $username, 'password' => $request->password])) {

          if ($request->remember) {
            $remember_token = Str::random(40);
            $user->update(['remember_token' => $remember_token]);
          }

          return $this->respondWithToken($token, $remember_token ?? null);
        }
      }
    }

    if (!count(array_filter($login_methods, function ($method) { return $method === true; })))
      return response()->json(['errors' => ['username' => ['exists']]], 401);

    return response()->json(['errors' => ['password' => ['password']]], 401);
  }

  public function user()
  {
    $user = auth('api')->user();

    if ($user) {
      $user->image;
      $user->unread_notifications = $user->notifications()->where('read_at', null)->count();
    }

    return response()->json($user ?? null);
  }

  public function logout()
  {
    if (auth()->id()) {
      auth()->user()->update(['remember_token' => null]);
      auth()->logout();
    }

    return response()->json(null, 200);
  }

  public function refresh()
  {
    return $this->respondWithToken(auth()->refresh());
  }

  public function respondWithToken($token, $remember_token = null)
  {
    $response = response()->json();

    $minutes = $remember_token ? 525600 : intval(config('session.lifetime'));
    $secure = !config('app.debug');

    # set token cookie
    $response->withCookie(cookie('token', $token, $minutes, '/', null, $secure, false));

    # set remember_me token
    if ($remember_token) $response->withCookie(cookie('remember_me', $remember_token, $minutes, '/', null, $secure, false));

    return $response;
  }
}
