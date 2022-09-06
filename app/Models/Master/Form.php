<?php

namespace App\Models\Master;

use App\Models\BaseModel;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends BaseModel
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    public $fillable = [
        'name',
        'icon',
        'cols',
        'order',
        'width',
        'dir',
        'class',
        'style',
        'active',
        'show_in_sales',
        'show_in_purchases',
        'show_in_forms',
    ];

    protected $casts = [
        'name'              => 'array',
        'order'             => 'integer',
        'active'            => 'boolean',
        'show_in_sales'     => 'boolean',
        'show_in_purchases' => 'boolean',
        'show_in_forms'     => 'boolean',
    ];

    
    # observation
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($form) {
            $form->fields()->delete();
        });
    }


    # relationships

    public function fields()
    {
        return $this->hasMany(Field::class)->withTrashed()->orderBy('order');
    }
}
