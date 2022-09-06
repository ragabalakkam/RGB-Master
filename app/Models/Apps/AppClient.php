<?php

namespace App\Models\Apps;

use App\Models\BaseModel;

# models
use App\Models\Clients\Client;
use App\Models\Versions\Version;
use App\Models\Master\BusinessType;
use App\Models\User;

# facades
use App\Helpers\Classes\MysqlDB;

# traits
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasImage;

class AppClient extends BaseModel
{
    use SoftDeletes, HasImage;

    public $table = 'apps_clients';

    protected $primaryKey = 'id';
    public $incrementing = false;

    public $fillable = [

        # app-specific
        'id',
        'secret',
        'name',
        'configurations',

        # domain
        'domain',
        'root_dir',

        # relationships
        'user_id',
        'app_id',
        'client_id',
        'version_id',
        'business_type_id',

        # database
        'db_driver',
        'db_host',
        'db_database',
        'db_username',
        'db_password',

        # flags
        'active',

        # timestamps
        'installed_at',
        'licensed_at',
        'checked_for_updates_at',

        # active process
        'active_process',
        'started_process_at',
        'installation_time',
        'uninstallation_time',
        'update_time',
    ];

    protected $casts = [
        'name'                  => 'array',
        'configurations'        => 'array',

        'app_id'                => 'integer',
        'client_id'             => 'integer',
        'version_id'            => 'integer',
        'business_type_id'      => 'integer',

        'installation_time'     => 'float',
        'uninstallation_time'   => 'float',
        'update_time'           => 'float',
    ];

    protected $dates = [
        'started_process_at',
        'installed_at',
        'licensed_at',
    ];

    protected $appends = [
        'version_number',
    ];


    // appends

    public function getVersionNumberAttribute()
    {
        return $this->version->number ?? null;
    }


    // scopes

    public function scopeInstalled($query)
    {
        return $query->whereNotNull('installed_at');
    }

    public function scopeLicensed($query)
    {
        return $query->whereNotNull('licensed_at');
    }

    public function scopeOnline($query)
    {
        return $query->where('online', true);
    }

    public function scopeProtected($query)
    {
        return $query->select([
            'id',
            'name',
            'client_id',
            'app_id',
            'business_type_id',
            'domain',
            'version_id',
            'active_process',
            'started_process_at',
            'installation_time',
            'uninstallation_time',
            'update_time',
            'created_at'
        ])->with(['image', 'version']);
    }


    // relationships

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    public function business_type()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function app_model()
    {
        return $this->app->model ? $this->app->model::find($this->id) : null;
    }

    public function app_db()
    {
        return new MysqlDB($this->db_database, $this->db_username, $this->db_password);
    }


    // functions

    public function log($content = null)
    {
        $timestamp = date('Y-m-d H:i:s');
        return _log("installations/{$this->id}", ["[$timestamp] $content"]);
    }

    public function start_process($process)
    {
        $PROCESS = strtoupper($process);
        $this->log("$PROCESS STARTED");

        return $this->update([
            'active_process'        => $process,
            'started_process_at'    => now(),
            $process . '_time'      => null,
        ]);
    }

    public function end_process($process, $success = null)
    {
        $time_column = $process . '_time';

        if (in_array($time_column, $this->getFillable()))
        {
            $time_diff = time() - strtotime($this->started_process_at);
            $this->update([$time_column => $time_diff > 1000000 ? 0 : $time_diff]);
        }

        $this->update([
            'active_process'        => null,
            'started_process_at'    => null,
        ]);

        $PROCESS = strtoupper($process);
        $this->log(is_null($success) ? "$PROCESS ENDED" : ($success ? "$PROCESS FINISHED SUCCESSFULLY" : "$PROCESS FAILED"));

        return $time_diff ?? null;
    }

    public function check_for_updates()
    {
        $this->log('checked for updates');
        $this->update(['checked_for_updates_at' => now()]);
    }

    public function setInstalled($installed = true)
    {
        return $this->update(['installed_at' => $installed ? now() : null]);
    }

    public function setLicensed($licensed = true)
    {
        return $this->update(['licensed_at' => $licensed ? now() : null]);
    }

    public function setVersion($version)
    {
        return $this->update(['version_id' => is_a($version, Version::class) ? $version->id : $version]);
    }
}
