<?php

namespace App\Models\Apps;

# models

use App\Models\Apps\Configurations\AppConfiguration;
use Illuminate\Database\Eloquent\Model;

# models
use App\Models\Clients\Client;
use App\Models\Versions\Version;
use App\Models\Master\BusinessType;
use App\Models\Apps\Configurations\AppConfigurationsGroup;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasImage;

class App extends Model
{
    use HasFactory, HasImage;

    public $fillable = [
        'name',
        'description',
        'model',
        'online',
    ];

    public $casts = [
        'name'          => 'array',
        'description'   => 'array',
        'online'        => 'boolean',
    ];

    public $appends = [
        'latest_version_id',
    ];


    // appends

    public function getLatestVersionIdAttribute()
    {
        return $this->versions->where('type', 'patch')->sortByDesc('created_at')->first()->id ?? null;
    }


    // relationships

    public function configurations()
    {
        return $this->hasMany(AppConfiguration::class);
    }

    public function configuration_groups()
    {
        return $this->hasMany(AppConfigurationsGroup::class)->with('configurations');
    }

    public function versions()
    {
        return $this->hasMany(Version::class);
    }

    public function business_types()
    {
        return $this->hasMany(BusinessType::class);
    }

    public function clients()
    {
        return $this->hasManyThrough(Client::class, AppClient::class, 'app_id', 'id', 'id', 'client_id');
    }

    public function client_apps()
    {
        return $this->hasMany(AppClient::class);
    }


    // functions

    public function getLatestVersion()
    {
        return $this->versions->sortByDesc('created_at')->first();
    }
}
