<?php

namespace App\Models\Locations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    public $timestamps = null;

    protected $fillable = [
        'id',
        'name',
        'state_id',
    ];

    protected $casts = [
        'name' => 'array',
        'state_id' => 'integer',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
