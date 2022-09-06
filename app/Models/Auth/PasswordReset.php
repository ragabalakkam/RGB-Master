<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{

    const UPDATED_AT = null;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $table = 'password_resets';

    protected $fillable = [
        'user_id',
        'token',
    ];

    # relationships

    protected function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
