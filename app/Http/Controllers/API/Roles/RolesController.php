<?php

namespace App\Http\Controllers\API\Roles;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Roles\RoleRequest;

# models
use App\Models\Roles\Role;
use App\Models\Roles\PermissionsGroup;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $employee = auth()->user()->employee();

        foreach (PermissionsGroup::all() as $group) {
            $permissions = $group->permissions;
            $permissions = array_filter($permissions->toArray(), function ($perm) use ($employee) {
                return $employee->hasPermission($perm['name']);
            });

            if (count($permissions)) {
                unset($group->permissions);
                $group->permissions = $permissions;
                $groups[] = $group;
            }
        }

        return response()->json([
            'roles'              => $roles,
            'permissions_groups' => $groups ?? [],
            'highest_priority'   => Role::getHighestPriority(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create($request->only(['name', 'priority']));
        $role->set_permissions($request->permission_ids);
        return response()->json($role);
    }

    public function show(Role $role)
    {
        return response()->json($role);
    }

    public function update(Role $role, RoleRequest $request)
    {
        $role->update($request->only(['name', 'priority']));
        $role->set_permissions($request->permission_ids);
        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json();
    }
}
