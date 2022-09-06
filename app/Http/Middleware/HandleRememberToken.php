<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

# models
use App\Models\User;

class HandleRememberToken
{
    public function handle(Request $request, Closure $next)
    {
        $remember_token = $_COOKIE['remember_me'] ?? null;

        if ($remember_token && isset($_COOKIE['token'])) {
            $user = User::where('remember_token', $remember_token)->first();
            if ($user) {
                $token = auth()->login($user);
                $cookie = cookie('token', $token, 525600, '/', null, false, false);
            }
        }

        $response = $next($request);

        return isset($cookie) ? $response->withCookie($cookie) : $response;
    }
}
