<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'key',
        'datatype',
        'value',
        'hidden',
        'disabled',
    ];

    public function getValueAttribute($value)
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

    public function setValueAttribute($value)
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

        $this->attributes['value'] = $func ? $func($value) : $value;
    }

    public function configurable()
    {
        return $this->morphTo();
    }
}
