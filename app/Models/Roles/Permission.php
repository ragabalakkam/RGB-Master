<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'permissions_group_id',
    ];



    # relationships

    public function group()
    {
        return $this->belongsTo(PermissionsGroup::class, 'permissions_group_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
