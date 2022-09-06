<?php

namespace App\Models\Lang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'label',
        'dir',
    ];

    protected $casts = [
        'name' => 'array',
    ];



    # relationships

    public function translations()
    {
        return $this->hasMany('App\Models\Lang\Translation');
    }
}
