<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    const UPDATED_AT = null;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $table = 'email_verifications';

    protected $fillable = [
        'user_id',
        'token',
    ];

    // relationships

    protected function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
