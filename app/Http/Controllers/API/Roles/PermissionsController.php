<?php

namespace App\Http\Controllers\API\Roles;

use App\Http\Controllers\Controller;

# models
use App\Models\Roles\permission;

# requets
use Illuminate\Http\Request;
use App\Http\Requests\Roles\PermissionRequest;

class PermissionsController extends Controller
{
    public function index()
    {
        $permissions = permission::all();
        $guarded = array_filter($permissions->whereNotNull('module')->toArray(), function ($perm) { return hasModule($perm['module']); });
        $not_guarded = $permissions->whereNull('module')->toArray();
        return response()->json(array_merge($guarded, $not_guarded));
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create($request->all());
        return response()->json($permission);
    }

    public function show(Permission $permission)
    {
        return response()->json($permission);
    }

    public function update(Permission $permission, PermissionRequest $request)
    {
        $permission->update($request->all());
        return response()->json($permission);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(null);
    }
}
