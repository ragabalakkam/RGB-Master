<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class PhoneVerification extends Model
{
    const UPDATED_AT = null;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $table = 'phone_verifications';

    protected $fillable = [
        'phone',
        'token',
        'verified_at',
    ];
}
