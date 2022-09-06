<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateClientRequest
{
    public function handle(Request $request, Closure $next)
    {
        $client = $request->route('client');
        $client_app = $request->route('app');
        
        # validate that given app belongs to given client
        if ($client_app->client_id != $client->id)
            return response()->json(['errors' => ['client_id' => ['invalid'], 'app_id' => ['invalid']]], 422);

        # validate app secret
        elseif ($request->secret != $client_app->secret)
            return response()->json(['errors' => ['secret' => ['invalid']]], 422);

        return $next($request);
    }
}
