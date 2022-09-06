<?php

namespace App\Models\Users;

# models
use App\Models\User;

class Customer extends User
{
    protected $table = 'users';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        foreach ($attributes as $key => $val) { $this->{$key} = $val; }
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('role', 'customer');
        });
    }

    #

    public function organizations()
    {
        return $this->belongsToMany('App\Models\Clients\Client', 'client_user', 'user_id');
    }
}
