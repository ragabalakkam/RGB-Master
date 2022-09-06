<?php

namespace App\Models\Clients;

# models
use App\Models\BaseModel;
use App\Models\User;
use App\Models\Apps\App;
use App\Models\Apps\AppClient;

# traits
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasImage;
use Exception;

# facades
use Illuminate\Support\Str;

class Client extends BaseModel
{
    use HasFactory, SoftDeletes, HasImage;

    public $fillable = [
        'name',
        'slogan',
        'phone',
        'email',
        'tax_number',
        'commercial_reg_no',

        'address',
        'full_address',

        'user_id',

        'notes',
        'extra',

        'online',
    ];

    public $casts = [
        'name'          => 'array',
        'slogan'        => 'array',

        'address'       => 'array',
        'full_address'  => 'array',

        'notes'         => 'array',
        'extra'         => 'array',

        'user_id'       => 'integer',

        'online'        => 'boolean',
    ];


    // relationships

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client_apps()
    {
        return $this->hasMany(AppClient::class)->with(['creator']);
    }

    public function apps()
    {
        return $this->hasManyThrough(App::class, AppClient::class, 'app_id', 'id', 'id', 'client_id')->with('image');
    }


    // functions

    public function attach_app($app, $data = [])
    {
        if (!is_a($app, App::class))
            $app = App::find($app);

        if (!$app)
            throw new Exception('No query results for ' . $app);

        $class = $app->model ?? AppClient::class;

        return $class::create([
            'id'                => strval(rand(1000000000, 9999999999)),
            'secret'            => Str::random(40),
            'configurations'    => $data['configurations'] ?? null,

            'name'              => $this->name,
            
            'domain'            => $data['domain'] ?? null,
            'root_dir'          => $data['root_dir'] ?? null,

            'user_id'           => auth()->id() ?? null,

            'app_id'            => $app->id,
            'client_id'         => $this->id,
            'version_id'        => $data['version_id'] ?? $app->getLatestVersion()->id ?? null,
            'business_type_id'  => $data['business_type_id'] ?? null,
            
            'db_driver'         => $data['db_driver'] ?? null,
            'db_host'           => $data['db_host'] ?? null,
            'db_database'       => $data['db_database'] ?? null,
            'db_username'       => $data['db_username'] ?? null,
            'db_password'       => $data['db_password'] ?? null,

            'active'            => true,
            'licensed'          => false,
        ]);
    }
}
