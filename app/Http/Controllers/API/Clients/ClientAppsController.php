<?php

namespace App\Http\Controllers\API\Clients;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;

# models
use App\Models\Apps\AppClient;
use App\Models\Master\BusinessType;
use App\Models\Versions\Version;

# jobs
use App\Jobs\Clients\InstallAppJob;
use App\Jobs\Clients\UninstallAppJob;
use App\Jobs\Clients\UpdateAppJob;

# facades
use App\Helpers\Classes\RemoteManagement\RemoteAppManager;
use App\Helpers\Classes\MysqlDB;


class ClientAppsController extends Controller
{
    private $queue_name = 'client_apps';

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(AppClient $app_client)
    {
        return response()->json($app_client);
    }

    public function update(AppClient $app_client, Request $request)
    {
        //
    }

    public function destroy(AppClient $app_client)
    {
        //
    }

    // 

    public function install(AppClient $app_client)
    {
        $app_client->start_process('installation');
        InstallAppJob::dispatch($app_client)->onQueue($this->queue_name);
        return response()->json($app_client);
    }

    public function uninstall(AppClient $app_client)
    {
        $app_client->start_process('uninstallation');
        UninstallAppJob::dispatch($app_client)->onQueue($this->queue_name);
        return response()->json($app_client);
    }

    public function license(AppClient $app_client)
    {
        if ($app_client->installed_at && $app = $app_client->app_model())
            $app->license();

        $app_client->update(['licensed_at' => now()]);
        return response()->json($app_client);
    }

    public function unlicense(AppClient $app_client)
    {
        if ($app_client->installed_at && $app = $app_client->app_model())
            $app->unlicense();

        $app_client->update(['licensed_at' => null]);
        return response()->json($app_client);
    }

    public function update_business_type(AppClient $app_client, Request $request)
    {
        $type = BusinessType::find($request->business_type_id);

        if (!$type)
            return response()->json(['errors' => ['business_type_id' => ['exists']]], 422);
        
        if ($app = $app_client->app_model())
        {
            if ($app_client->installed_at)
                $app->updateBusinessType($type, !$request->update_configurations, $request->update_database);

            else if ($request->update_configurations)
                $app_client->update(['configurations' => $request->configurations]);
        }

        $app_client->update(['business_type_id' => $type->id]);
        return response()->json(AppClient::find($app_client->id));
    }

    public function update_configurations(AppClient $app_client, Request $request)
    {
        $keys = [];
        foreach ($app_client->app->configuration_groups->whereIn('key', ['modules', 'organization']) as $group) {
            $keys = [...$keys, ...$group->configurations->pluck('key')->toArray()];
        }
        if ($app_client->installed_at && $app = $app_client->app_model())
        {
            foreach ($keys as $key) {
                $app->setConfig($key, $request->configurations[$key]);
            }
            $app->updateConfigurations();
        }
        $app_client->update(['configurations' => $request->configurations]);
        return response()->json(AppClient::find($app_client->id));
    }

    public function update_domain(AppClient $app_client, Request $request)
    {
        if ($app_client->installed_at && $app = $app_client->app_model())
            $app->updateSubdomain($request->domain, $request->root_dir);

        $app_client->update([
            'domain'    => $request->domain,
            'root_dir'  => $request->root_dir,
        ]);

        return response()->json($app_client);
    }

    public function update_database(AppClient $app_client, Request $request)
    {
        if ($app_client->installed_at && $app = $app_client->app_model())
        {
            # take a backup
            $old_db = new MysqlDB($app->db_database, $app->db_username, $app->db_password);
            $backup = str_replace(public_path('storage/'), '', $old_db->export());

            # create database & user with all privileges
            $app->updateDatabaseCredentials($request->db_database, $request->db_username, $request->db_password);

            # import the latest a backup
            $new_db = new MysqlDB($app->db_database, $app->db_username, $app->db_password);
            $new_db->import(public_path("storage/$backup"));

            # drop old database & delete user
            $old_db->drop();
            $old_db->deleteUser();
        }
        
        $app_client->update([
            'db_database' => $request->db_database,
            'db_username' => $request->db_username,
            'db_password' => $request->db_password,
        ]);

        return response()->json($app_client);
    }

    public function import_database(AppClient $app_client, Request $request)
    {
        if ($app = $app_client->app_model())
        {
            # backup database
            $app->exportDatabase();

            # drop & create a new 
            MysqlDB::dropDB($app->db_database, $app->db_username, $app->db_password);
            MysqlDB::createDB($app->db_database, $app->db_username, $app->db_password);

            # import param file
            $app->importDatabase(public_path('storage/' . $request->file->store('databases', 'public')));
        }

        return response()->json($app_client);
    }

    public function export_database(AppClient $app_client)
    {
        if ($app = $app_client->app_model())
        {
            # backup database
            $backup_path = $app->exportDatabase();
        }
        else
        {
            $db = new MysqlDB($app_client->db_database, $app_client->db_username, $app_client->db_password);
            $backup_path = str_replace(public_path('storage/'), '', $db->export());
        }
        return response()->json($backup_path);
    }

    public function update_version(AppClient $app_client, Request $request)
    {
        if ($app_client->installed_at)
        {
            $app_client->start_process('update');
            UpdateAppJob::dispatch($app_client, Version::find($request->version_id ?? $app_client->app->latest_version_id))->onQueue($this->queue_name);
            // return response()->json(['errors' => ['version_id' => ['somethingWentWrong']]], 422);
        }
        else $app_client->update(['version_id' => $request->version_id]);
        return response()->json($app_client);

    }

    public function clean_database(AppClient $app_client)
    {
        $backup = $this->export_database($app_client)->original;

        if ($app = $app_client->app_model())
        {
            $app->importDatabase();
        }
        else
        {
            MysqlDB::dropDB($app_client->db_database);
            MysqlDB::createDB($app_client->db_database);

            $sql_path = $app_client->root_dir . '/database.sql';
            if (file_exists($sql_path))
            {
                $db = new MysqlDB($app_client->db_database, $app_client->db_username, $app_client->db_password);
                $db->import($sql_path);
            }
        }

        return response()->json($backup);
    }

    public function check_for_updates(AppClient $app_client)
    {
        $output = RemoteAppManager::artisan($app_client, 'version:update');
        return response()->json($output);
    }
}
