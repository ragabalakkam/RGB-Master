<?php

namespace App\Models\Lang;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable  = [
        'locale_id',
        'key',
        'value',
        'description',
        'translation_group_id',
    ];

    protected $hidden = [
        'locale_id',
        'created_at',
        'updated_at',
    ];

    # relationships

    public function locale()
    {
        return $this->belongsTo('App\Models\Lang\Locale');
    }
}
