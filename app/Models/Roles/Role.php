<?php

namespace App\Models\Roles;

use App\Models\BaseModel;
use App\Models\Users\Employee;

class Role extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'key',
        'name',
        'priority',
    ];

    protected $casts = [
        'name'      => 'array',
        'priority'  => 'integer',
    ];

    protected $hidden = [
        'key',
    ];


    # appends

    protected $appends = [
        'permission_ids',
    ];

    public function getPermissionIdsAttribute()
    {
        return $this->permissions()->pluck('id')->toArray();
    }


    # observers

    public static function onCreating($role)
    {
        $key = normalize($role->name['en'], 'role');
        while (self::where('key', $key)->count()) {
            $key .= rand(1, 9999);
        }
        $role->key = $key;
    }


    # relationships

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function employees()
    {
        return $this->hasManyThrough(Employee::class, RoleUser::class, 'role_id', 'id', 'id', 'user_id');
    }


    # functions

    public static function getHighestPriority()
    {
        return self::all()->max('priority');
    }

    public function set_permissions($permission_ids = [])
    {
        # unlink non-related permissions
        $this->permissions()->detach(array_diff($this->permission_ids, $permission_ids));

        # link new permissions
        foreach ($permission_ids as $id) {
            PermissionRole::updateOrCreate([
                'permission_id' => $id,
                'role_id' => $this->id,
            ]);
        }
    }
}
