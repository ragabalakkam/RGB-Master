<?php

namespace App\Models\Apps\Configurations;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfiguration extends Model
{
    use HasFactory;

    public $table = 'app_configurations';

    public $timestamps = false;

    public $fillable = [
        'name',
        'description',
        'key',
        'datatype',
        'default',
        'app_id',
        'required',
        'hidden',
        'disabled',
    ];

    public $casts = [
        'name'  => 'array',
    ];

    public function getDefaultAttribute($value)
    {
        $func = null;

        switch ($this->datatype)
        {
            case 'boolean':
                $func = 'boolval';
                break;
            case 'float':
                $func = 'floatval';
                break;
            case 'integer':
                $func = 'intval';
                break;
            case 'string':
                $func = 'strval';
                break;
            case 'array':
                $func = 'json_decode';
                break;
        }

        return $func ? $func($value) : $value;
    }

    public function setDefaultAttribute($value)
    {
        $func = function ($x) { return $x; };

        switch ($this->datatype)
        {
            case 'timestamp':
                $func = function ($val) { return $val ? Carbon::parse($val)->format('Y-m-d\TH:i:s\Z') : $val; };
                break;
            case 'boolean':
                $func = 'boolval';
                break;
            case 'float':
                $func = 'floatval';
                break;
            case 'integer':
                $func = 'intval';
                break;
            case 'string':
                $func = 'strval';
                break;
            case 'array':
                $func = 'json_encode';
                break;
        }

        $this->attributes['default'] = $func ? $func($value) : $value;
    }


    // relationships

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
