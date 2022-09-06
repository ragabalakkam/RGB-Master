<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

# models
use App\Models\Apps\App;

class BusinessType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cashier_settings',
        'modules',
        'sales_systems',
        'translations',
        'forms',
        'database',
        'zip_path',
        'app_id',
    ];

    protected $casts = [
        'name'              =>  'array',
        'cashier_settings'  =>  'array',
        'modules'           =>  'array',
        'sales_systems'     =>  'array',
        'translations'      =>  'array',
        'forms'             =>  'array',
        'database'          =>  'array',
        'app_id'            =>  'integer',
    ];



    // relationships

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
