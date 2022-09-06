<?php

namespace App\Models\Apps\Configurations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AppConfigurationGroup extends Pivot
{
    use HasFactory;

    public $table = 'app_configuration_group';

    public $timestamps = false;

    public $fillable = [
        'app_configuration_id',
        'app_configuration_group_id',
    ];


    // relationships

    public function group()
    {
        return $this->belongsTo(AppConfigurationsGroup::class);
    }

    public function configuration()
    {
        return $this->belongsTo(AppConfiguration::class);
    }
}
