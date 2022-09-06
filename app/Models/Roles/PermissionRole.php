<?php

namespace App\Models\Roles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'permission_id',
    ];



    # relationships

    public function role()
    {
        return $this->belongsTo('App\Models\Roles\Role');
    }

    public function permission()
    {
        return $this->belongsTo('App\Models\Roles\Permission');
    }
}
