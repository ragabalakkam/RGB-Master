<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    public $timestamps = null;


    protected $fillable = [
        'name',
        'city_id',
        'neCoordinates',
        'swCoordinates',
    ];

    protected $casts = [
        'name' => 'array',
        'city_id' => 'integer',
    ];

    protected $hidden = [
        'neCoordinates',
        'swCoordinates',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
