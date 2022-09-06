<?php

namespace App\Http\Middleware;

use App\Models\Roles\Action;
use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        $action = $request->route()->getAction()['as'] ?? null;
        if (!$action) return $next($request);
        
        $action = str_replace('show', 'index', $action);
        $action = Action::where('name', $action)->first();

        $user = auth()->user();
        $emp = $user->employee();

        if (
            # pass if no permission is required
            !$action

            ||

            # pass if user is an employee who has the required permission to go through
            ($emp && $emp->hasPermission($action->permission->name))
        )
            return $next($request);

        # forbidden access
        return response()->json(['message' => 'forbidden'], 403);
    }
}
