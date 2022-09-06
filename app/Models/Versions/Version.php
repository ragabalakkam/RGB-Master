<?php

namespace App\Models\Versions;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

# models
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Clients\Client;
use App\Models\Apps\App;

class Version extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'description',
        'notes',
        'path',
        'type',
        'major',
        'minor',
        'patch',
        'stable',
        'app_id',
        'user_id',
    ];

    public $casts = [
        'major'     => 'integer',
        'minor'     => 'integer',
        'patch'     => 'integer',
        'stable'    => 'boolean',
        'app_id'    => 'integer',
        'user_id'   => 'integer',
    ];

    public $hidden = [
        'major',
        'minor',
        'patch',
    ];

    public $appends = [
        'number',
    ];


    # appends

    public function getNumberAttribute()
    {
        return 'v' . $this->major . '.' . $this->minor . '.' . $this->patch;
    }


    # observers

    public static function boot()
    {
        parent::boot();

        $update_major_minor_patch = function ($version)
        {
            $latest_version = $version->app->versions()->where('id', '!=', $version->id ?? null)->orderBy('created_at', 'DESC')->first();
        
            $is_major = $version->type == 'major';
            $is_minor = $version->type == 'minor';
            $is_patch = $version->type == 'patch';
        
            $version->major = ($latest_version->major ?? 0) + ($is_major ? 1 : 0);
            $version->minor = $is_major ? 0 : ($is_minor ? ($latest_version->minor ?? -1) + ($is_minor ? 1 : 0) : ($latest_version->minor ?? 0));
            $version->patch = $is_major || $is_minor ? 0 : ($is_patch ? ($latest_version->patch ?? -1) + ($is_patch ? 1 : 0) : ($latest_version->patch ?? 0));
        };
        self::creating($update_major_minor_patch);
        self::updating($update_major_minor_patch);
    }


    # scopes

    public function scopeProtected($query)
    {
        return $query->select(['id', 'description', 'created_at', 'type', 'major', 'minor', 'patch', 'stable']);
    }


    # relationships

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}