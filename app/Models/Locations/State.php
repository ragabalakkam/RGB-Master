<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public $timestamps = null;

    protected $fillable = [
        'name',
        'country_id',
    ];

    protected $casts = [
        'name' => 'array',
        'country_id' => 'integer',
    ];



    # relationships

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
