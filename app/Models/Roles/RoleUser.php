<?php

namespace App\Models\Roles;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
    ];



    # relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
