<?php

namespace App\Models\Users;

# models
use App\Models\User;
use App\Models\Roles\Role;
use App\Models\Roles\Permission;

class Employee extends User
{
    protected $table = 'users';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        foreach ($attributes as $key => $val) {
            $this->{$key} = $val;
        }
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('role', 'employee');
        });
    }

    #

    public function hasPermission($permission_name)
    {
        if (!($permission = Permission::where('name', $permission_name)->first()))
            return false;

        foreach ($this->roles as $role) {
            if (in_array($permission->id, $role->permission_ids))
                return true;
        }

        return false;
    }

    public function giveRole($role)
    {
        if (is_string($role))
            $role = Role::where("name", "like", "%$role%")->first();

        if (!$role)
            throw new \Exception("Cannot find role $role");

        if (!is_a($role, Role::class))
            throw new \Exception("Role " . castNull($role->name) . " is not an instance of Role");

        return $this->roles()->attach($role);
    }

    public function set_roles(array $roles_ids)
    {
        $existing_role_ids = $this->roles->pluck('id')->toArray();

        foreach (Role::whereNotIn('id', $existing_role_ids)->whereIn('id', $roles_ids)->get() as $role) {
            $this->giveRole($role);
        }

        $this->roles()->detach(array_diff($existing_role_ids, $roles_ids));
        unset($this->roles);
        $this->roles = $roles_ids;

        return $this;
    }
}
