<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public $timestamps = null;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'array',
    ];



    # relationships

    protected function states()
    {
        return $this->hasMany(State::class);
    }
}
