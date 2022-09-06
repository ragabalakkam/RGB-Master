<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionsGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];



    # relationships

    public function permissions()
    {
        return $this->hasMany('App\Models\Roles\Permission', 'permissions_group_id', 'id');
    }
}
