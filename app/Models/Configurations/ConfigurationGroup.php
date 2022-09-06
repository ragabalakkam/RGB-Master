<?php

namespace App\Models\Configurations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;

# models
use App\Models\Configuration;

class ConfigurationGroup extends BaseModel
{
    use HasFactory;

    public $table = 'configuration_group';

    public $timestamps = false;

    protected $fillable = [
        'configuration_id',
        'configuration_group_id',
    ];

    protected $casts = [
        'configuration_id'          => 'integer',
        'configuration_group_id'    => 'integer',
    ];


    # relationships

    public function configuration()
    {
        return $this->belongsTo(Configuration::class);
    }

    public function group()
    {
    return $this->belongsTo(Group::class);
    }
}
