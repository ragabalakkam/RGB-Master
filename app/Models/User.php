<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasImage;

# models
use App\Models\Users\Employee;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes, HasImage, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'remember_token',

        'role',

        'email',
        'email_verified_at',

        'phone',
        'phone_verified_at',

        'address',
        'full_address',
        'location',

        'locale_id',

        'notes',
        'extra',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'address'       => 'array',
        'full_address'  => 'array',
        'location'      => 'array',
        'locale_id'     => 'integer',
        'extra'         => 'array',
    ];

    protected $dates = [
        'email_verified_at',
        'phone_verified_at',
    ];


    # appends

    protected $appends = [
        'role_ids',
        'priority',
    ];

    public function getRoleIdsAttribute()
    {
        return $this->roles()->pluck('id')->toArray() ?? [];
    }

    public function getPriorityAttribute()
    {
        return $this->roles()->max('priority') ?? 0;
    }



    # relationships

    protected function emailVerification()
    {
        return $this->hasOne('App\Models\Auth\EmailVerification', 'user_id');
    }

    protected function passwordReset()
    {
        return $this->hasOne('App\Models\Auth\PasswordReset', 'user_id');
    }

    public function notifications()
    {
        return $this->morphMany('App\Models\Notification', 'notifiable')->orderByDesc('created_at');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Roles\Role', 'role_user', 'user_id', 'role_id');
    }



    # methods


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'App.User.' . $this->id;
    }

    public function employee()
    {
        return new Employee($this->toArray());
    }
}
