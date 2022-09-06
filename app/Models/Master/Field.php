<?php

namespace App\Models\Master;

use App\Models\BaseModel;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends BaseModel
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    public $fillable = [
        'form_id',
        'name',
        'icon',
        'cols',
        'datatype',
        'order',
        'class',
        'style',
        'dir',
        'required',
        'active',
    ];

    protected $casts = [
        'form_id'   => 'integer',
        'name'      => 'array',
        'order'     => 'integer',
        'required'  => 'boolean',
        'active'    => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
