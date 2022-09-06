<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

# controllers
use App\Http\Controllers\API\Lang\LocalesController;
use App\Http\Controllers\API\Database\DatabaseController;

# models
use App\Models\User;
use App\Models\Clients\Client;
use App\Models\Apps\RGBOnline;

# facades
use Illuminate\Support\Str;
use App\Helpers\Classes\MysqlDB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

# jobs
use App\Jobs\Roles\NotifyMastersJob;

# notifications
use App\Notifications\Master\ClientApps\AppsBackedUpNotification;
use App\Notifications\Master\CollectedLogsNotification;
use App\Notifications\Master\DatabaseBackupNotification;


Artisan::command('clear', function ()
{
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('optimize');
    exec('composer clearcache');
    exec('npm cache clear --force');
    app('App\Http\Controllers\API\ConfigurationsController')->index();
    app('App\Http\Controllers\API\Lang\LocalesController')->export(null, 'cache');
});


Artisan::command('db:truncate {--seed}', function ($seed)
{
    $this->info("Starting truncating db tables..\r\n");
    
    Schema::disableForeignKeyConstraints();
    $this->info("Disabled Foreign Key Constraints \r\n");
    $tables = array_diff(array_map('current', DB::select('SHOW TABLES')), [
        'migrations',
        'locales',
        'translations',
        'countries',
        'states',
        'cities',
        'districts',
        // 'configurations',
        // 'accounts'
    ]);
    foreach ($tables as $table) {
        $this->info("〰 truncating $table table ...");
        DB::table($table)->truncate();
        $this->line("truncated $table table successfully \r\n");
    }
    Schema::enableForeignKeyConstraints();
    $this->info("Truncated all tables successfully! \r\n");
    $this->info("Enabled Foreign Key Constraints again \r\n");

    if ($seed) {
        Artisan::call('db:seed');
        $this->info("Database seeded successfully! \r\n");
    }
});


Artisan::command('make:all {param}', function ($param)
{
    # controller
    $name = Str::ucfirst(Str::plural($param), '/');
    Artisan::call('make:controller API/' . $name . 'Controller --api --type=model');
    $this->info("created controller");

    # request
    $name = Str::ucfirst(Str::singular($param), '/');
    Artisan::call('make:request ' . $name . 'Request');
    $this->info("created request");

    # model & migration
    $name = Str::ucfirst(Str::singular($param), '/');
    Artisan::call('make:model ' . $name . ' -m');
    $this->info("created model");
    $this->info("created migration");
})->describe('Build (migration / Model / Controller / Request) in one tab');


Artisan::command('seed {seeders?} {tables?}', function ($seeders = null, $tables = null)
{
    if ($tables) {
        Schema::disableForeignKeyConstraints();
        $this->info("Disabled Foreign Key Constraints \r\n");

        foreach(explode(',', $tables) as $table) {
            $this->info("〰 truncating accounts table ...");
            DB::table($table)->truncate();
            $this->line("truncated $table table successfully \r\n");
        }
        
        Schema::enableForeignKeyConstraints();
        $this->info("Enabled Foreign Key Constraints again \r\n");
    }

    if ($seeders) {
        foreach (explode(',', $seeders) as $seeder) {
            Artisan::call("db:seed --class=$seeder");
            $this->info("Seeded $seeder ! \r\n");
        }
    }
    $this->info("Database seeded successfully! \r\n");
});


Artisan::command('locales:update', function ()
{
    echo app(LocalesController::class)->factory_reset()->original;
})->describe('Reset all locales & translations to their defaults');


Artisan::command('clients:clear', function ()
{
    foreach (Client::withTrashed()->get() as $client)
    {
        foreach ($client->client_apps as $app)
        {
            MysqlDB::dropDB($app->db_database, 'root', $app->db_password);
            if (is_dir($app->root_dir)) remove_dir($app->root_dir);
        }
        $client->forceDelete();
        $this->info($client->name['en'] . ' : removed successfully !');
    }

    $this->newLine();

    $dirs_to_remove = [
        'storage\backups',
        'storage\client-backups',
        'storage\logs',
    ];
    foreach ($dirs_to_remove as $dir)
    {
        $dir = public_path($dir);
        is_dir($dir) && remove_dir($dir)
            ? $this->info("Cleared $dir successfully!")
            : $this->error("Couldn't remove $dir : not such file or directory");
    }

    $this->newLine();

    $this->info("Removed all clients successfully!");

})->describe('Removes all clients and their dirs & databases');


Artisan::command('clients:uninstall', function ()
{
    foreach (Client::withTrashed()->get() as $client)
    {
        foreach ($client->client_apps as $client_app)
        {
            if ($app = $client_app->app_model())
                $app->uninstall();            
        }
        $this->info($client->name['en'] . ' : uninstalled successfully !');
    }

    $this->newLine();

    $dirs_to_remove = [
        'storage\backups',
        'storage\clients',
        'storage\client-backups',
        'storage\logs',
    ];
    foreach ($dirs_to_remove as $dir)
    {
        $dir = public_path($dir);
        is_dir($dir) && remove_dir($dir)
            ? $this->info("Cleared $dir successfully!")
            : $this->error("Couldn't remove $dir : not such file or directory");
    }

    $this->newLine();

    $this->info("Uninstalled all clients successfully!");

})->describe('uninstall all clients');


Artisan::command('links', function ()
{
    $this->info('PHP TEST PAGE  : ' . getConfig('url') . '/dev@afaqrgb/test');
    $this->info('[LOGS] QUERIES : C:\xampp\htdocs\rgbksa\master\public\storage\logs\queries.log');
    $this->info('[LOGS] CLIENTS : C:\xampp\htdocs\rgbksa\master\public\storage\logs\client-logs');
})->describe('Show some supportive links');


Artisan::command('version:update {--live} {--compile} {--muted}', function ($live, $compile, $muted) {
    
    $temp_dir = "C:/xampp/htdocs/temp" . Str::random(50);

    $this->newLine();

    # npm run prod
    if ($compile) {
        $this->info('running npm run prod ..');
        exec('npm run prod', $output);
        foreach ($output as $line) {
            $this->line($line);
        };
    }

    # copy files
    $to_copy = [
        '@factory-reset'  => [],
        'app'             => [],
        'config'          => [],
        'routes'          => [],
        'bootstrap'       => ['cache'],
        'resources'       => ['css', 'sass', 'js'],
        'public'          => [
            'downloads',
            '@cache',
            '@license',
            '@m',
            'fonts',
            'webfonts',
            'storage',
            'plugins',
            'imgs',
            'sounds',
            '.htaccess',
            'favicon.ico',
            'index.php',
            'mix-manifest.json',
            'robots.txt',
            'web.config',
            'css/theme.css',
            'css/print_vars.css',
            'css/fontawesome.css',
            'js/app.js.LICENSE.txt',
            'js/dev.js.LICENSE.txt',
            'js/auth.js.LICENSE.txt',
            'js/master.js.LICENSE.txt',
            'js/print.min.js',
            'js/fontawesome.min.js',
        ],
    ];
    foreach ($to_copy as $file => $except) {
        $file_path = base_path($file);
        $this->info($file);

        if (is_file($file_path)) {
            copy($file_path, "$temp_dir/$file");
            $this->line("copied $file_path");
        } else {
            recursive_copy($file_path, "$temp_dir/$file", $except, !$muted);
            $this->line("copied $file_path => $temp_dir/$file");
        }
    }

    $this->newLine();

    # export zip file
    $local_zip = getDesktopPath() . "/RGB-MASTER-UPDATE-" . date('Y-m-d') . ".zip";
    if (zip($temp_dir, $local_zip)) $this->info("Zipping : done successfully ! $local_zip");
    else $this->error('Zipping: cannot be done');

    # remove temp dir
    remove_dir($temp_dir);

    if ($live) {
        $ftp_file_name = 'MASTER-UPDATE.zip';

        if (Storage::disk('ftp')->put($ftp_file_name, fopen($local_zip, 'r+'))) $this->info("uploaded /RGB/$ftp_file_name");
        else $this->error("Cannot upload new version file to FTP server");

        unlink($local_zip);

        # perform update requests
        /* $response = */
        Http::get('https://master.rgbksa.com/dev@afaqrgb/test?func=update_master');
        /* if ($response->getStatusCode() == 200) */
        $this->info("Successfully updated remote master");
        /* else $this->error("Error while updating remote master : " . $response->body()); */
    }
    
})->describe('Update remote master whole version (files)');


Artisan::command('remote:update {--compile} {file?}', function ($file = 'resources\views\dev\{*,*/*}.php', $compile)
{
    if ($file)
    {
        $base_dir_length = strlen(base_path()) + 1;
        $files = glob(base_path($file), GLOB_BRACE);
        
        foreach ($files as $filepath)
        {
            $file = substr($filepath, $base_dir_length);
            if (Storage::disk('ftp')->put("master/$file", fopen($filepath, 'r+'))) $this->line("uploaded $file");
            else $this->error("Cannot upload new file to FTP server");
        }
        
        $this->info('Finished uploading process.');
    }
    else
    {
        Artisan::call('version:update --muted ' . ($compile ? '--compile' : null));
        $local_zip = getDesktopPath() . "/RGB-MASTER-UPDATE-" . date('Y-m-d') . ".zip";

        if (file_exists($local_zip))
        {
            $temp_dir = "C:/xampp/htdocs/temp/" . Str::random(50);
            create_dir_if_not_exist($temp_dir);
            extract_zip($local_zip, $temp_dir);

            $temp_dir_length = strlen($temp_dir) + 1;
            foreach(array_map(fn ($f) => substr($f, $temp_dir_length), listFiles($temp_dir)) as $file)
            {
                if (Storage::disk('ftp')->put("master/$file", fopen("$temp_dir/$file", 'r+'))) $this->line("uploaded $file");
                else $this->error("Cannot upload new file to FTP server");
            }

            remove_dir($temp_dir);
            $this->info('Finished uploading process.');
        }
    }
})->describe('Update remote master file(s)');


Artisan::command('database:backup', function ()
{
    $this->info('# ' . now());

    $response = app(DatabaseController::class)->backup();

    # error
    if ($response->getStatusCode() != 200)
        $this->error($response->original);

    $path = $response->original;
    $path ? $this->info("Backup done successfully : $path") : $this->error("Failed to locally backup database !");

    if ($path) NotifyMastersJob::dispatch(DatabaseBackupNotification::class, $path);

})->describe('Backup database');


Artisan::command('apps:database-backup {ids?}', function ($ids = null)
{
    $backups = [];

    $all = is_null($ids);
    $url = getConfig('url');

    $apps = $all ? RGBOnline::withoutTrashed() : RGBOnline::whereIn('id', explode(',', $ids));
    $apps = $apps->whereNotNull('db_database')->installed()->get();

    foreach ($apps as $app) {
        $backup = $app->exportDatabase();
        $backups[$app->id] = $backup;
        $this->info('Finished backup for ' . (implode(' | ', array_values($app->name))) . ' at ' . date('Y-m-d H:i:s', time()) . '.');
        $this->line("$url/storage/$backup");
    }

    if ($all) NotifyMastersJob::dispatch(AppsBackedUpNotification::class, $backups);
    
})->describe('Backup client(s) databases. Given app id or it takes backup for all installed apps');


Artisan::command('logs:collect', function ()
{
    $dest_dir = "/home/rgbksaco/RGB/logs";
    $emails = ['minaalfy8@gmail.com'];
    create_dir_if_not_exist($dest_dir);

    # client logs
    $paths = array_map(
        fn ($dir) => "$dir/storage/logs/laravel.log",
        RGBOnline::installed()->whereNotNull('root_dir')->pluck('root_dir', 'name->en AS name')->toArray()
    );

    # master logs
    $paths['Master'] = base_path('storage/logs/laravel.log');

    # logs
    $logs = array_filter($paths, 'file_exists');

    # collect them to $dest
    foreach ($logs as $name => $log) {
        rename($log, "$dest_dir/$name.log");
        $logs[$name] = "$dest_dir/$name.log";
    }

    if (count($logs))
    {
        $users = User::whereIn('email', $emails)->get();
        $count = $users->count();
        foreach ($logs as $name => $log) { $this->line("$name : $log"); }
        foreach ($users as $i => $user) { $user->notify(new CollectedLogsNotification($logs, $i + 1 == $count)); }
    }
    else $this->info('No logs found');
    
})->describe('Collect all laravel logs from online clients & send them via email');