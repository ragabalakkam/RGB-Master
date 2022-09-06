<?php

namespace App\Models\Apps\Configurations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppConfigurationsGroup extends Model
{
    use HasFactory;

    public $table = 'app_configuration_groups';

    public $timestamps = false;

    public $fillable = [
        'name',
        'description',
        'key',
        'app_id',
        'hidden',
        'disabled',
    ];

    public $casts = [
        'name'  => 'array',
    ];


    // relationships

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function configurations()
    {
        return $this->hasManyThrough(AppConfiguration::class, AppConfigurationGroup::class, 'app_configuration_group_id', 'id', 'id', 'app_configuration_id');
    }
}
