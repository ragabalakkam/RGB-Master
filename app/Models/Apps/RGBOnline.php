<?php

namespace App\Models\Apps;

# requests
use Illuminate\Http\Request;

# models
use App\Models\Versions\Version;
use App\Models\Master\BusinessType;

# facades
use App\Helpers\Classes\MysqlDB;
use App\Helpers\Classes\CPanel;
use Illuminate\Support\Str;
use App\Helpers\Classes\Dir;
use App\Helpers\Classes\Zip;
use Exception;

# rules
use App\Rules\UniqueExceptCurrent;

class RGBOnline extends AppClient
{
    public $table = 'apps_clients';

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('app_id', 1);
        });
    }

    #

    public function updateSubdomain($domain = null, $root_dir = null)
    {
        if ($root_dir && file_exists($this->root_dir) && $this->root_dir != $root_dir)
        {
            $storage_link = "{$this->root_dir}/public/storage";
            remove_link($storage_link);

            rename($this->root_dir, $root_dir);
            $this->log("moved dir : {$this->root_dir} => $root_dir");
        }

        $this->update([
            'domain'    => $domain ?? $this->domain,
            'root_dir'  => $root_dir ?? $this->root_dir,
        ]);
        $this->log("updated domain : {$this->domain}");
        $this->log("updated root dir : {$this->root_dir}");

        # cPanel / Create Subdomain
        if ((!$domain || $this->domain != $domain) && config('app.live'))
        {
            $cpanel = new CPanel();
            $cpanel->createSubDomain(normalize_domain($this->domain), 'rgbksa.com', "{$this->root_dir}/public");
            $this->log("cPanel / created subdomain : " . normalize_domain($this->domain) . ", dir: {$this->root_dir}/public");
        }

        # update env vars
        $this->setEnv('APP_URL', $this->domain);

        # cache new configs
        $this->artisan('config:cache');

        # link storage again
        $this->artisan('storage:link');

        # update .htaccess file
        $this->updateHtaccess();
    }

    public function updateMasterDatabaseCredentials($database = null, $username = null, $password = null)
    {
        $this->setEnv('DB_MASTER_DATABASE', $database ?? 'rgbksaco_rgb_master');
        $this->setEnv('DB_MASTER_USERNAME', $username ?? 'rgbksaco_rgb_master');
        $this->setEnv('DB_MASTER_PASSWORD', $password ?? 'afaqrgb2000');

        # cache new configs
        $this->artisan('config:cache');
    }

    public function updateDatabaseCredentials($database = null, $username = null, $password = null)
    {
        if (!$database) $database = $this->db_database;
        if (!$username) $username = $this->db_username;
        if (!$password) $password = $this->db_password;

        # cPanel / Create database & user & give user all privileges
        if (config('app.live')) {
            $cpanel = new CPanel();
            $cpanel->createDatabase($database);
            $cpanel->createDatabaseUser($username, $password);
            $cpanel->setAllPrivilegesOnDatabase($username, $database);
        }
        else
        {
            $username = 'root';
            $password = 'afaqrgb2000';
            MysqlDB::createDB($database, $username, $password);
        }

        # update AppClient model
        $this->update([
            'db_database' => $database,
            'db_username' => $username,
            'db_password' => $password,
        ]);

        $this->log("updated db_database => $database");
        $this->log("updated db_username => $username");
        $this->log("updated db_password => $password");

        # set new ENV vars
        $this->setEnv('DB_DATABASE', $database);
        $this->setEnv('DB_USERNAME', $username);
        $this->setEnv('DB_PASSWORD', $password);

        # cache new configs
        $this->artisan('config:cache');
    }

    public function importDatabase($file_path = null)
    {
        # take backup
        $backup = $this->exportDatabase();

        # drop & create a new database
        if (config('app.live'))
        {
            $cpanel = new CPanel();
            $cpanel->deleteDatabase($this->db_database);
            $this->log("Dropped database :" . $this->db_database);
            $cpanel->createDatabase($this->db_database);
            $this->log("Re-created database :" . $this->db_database);
            $cpanel->setAllPrivilegesOnDatabase($this->db_username, $this->db_database);
            $this->log("set all privileges on database :" . $this->db_database);
        }
        else
        {
            MysqlDB::dropDB($this->db_database, $this->db_username, $this->db_password);
            MysqlDB::createDB($this->db_database, $this->db_username, $this->db_password);
        }

        # import database
        $time = microtime(true);
        $this->log("started importing database : {$this->db_database}");
        $result = $this->app_db()->import($file_path ?? ($this->root_dir . "/database.sql"));
        $this->log("imported database : {$this->db_database} (" . calcTime($time) . ")");

        # clear
        $this->artisan('clear');

        return $result ? $backup : $result;
    }

    public function exportDatabase()
    {
        $db = $this->app_db();

        if (count($db->showTables()))
        {
            $app_name = parseName($this->client->name, 'en') . ' - ' . parseName($this->client->name, 'ar');
            $time = microtime(true);

            # take backup
            $this->log("Started database backup {$this->db_database}");
            $backup = str_replace(public_path('storage/'), '', $db->export());
            $src = public_path("storage/$backup");
            $this->log("Database {$this->db_database} backup finished : $src (" . calcTime($time) . ")");

            # copy to clients' files
            $this->log("Copying database backup {$this->db_database}");
            $dest = "{$this->root_dir}/storage/app/public/$backup";
            Dir::remove(dirname($dest));
            copy($src, $dest);
            $this->log("Database backup copied $src => $dest (" . calcTime($time) . ")");

            # copy to master FTP
            $this->log("Copying database backup {$this->db_database} to ftp");
            $dest = getConfig("ftp_dir") . "/backups/$app_name/" . time() . "/" . basename($backup);
            Dir::remove(dirname($dest));
            copy($src, $dest);
            $this->log("Database backup copied to ftp $src => $dest (" . calcTime($time) . ")");

            # move to client-backups
            $this->log("Moving database backup {$this->db_database} to client-backups");
            $dest = str_replace("backups", "client-backups/$app_name", $backup);
            Dir::remove(dirname(public_path("storage/$dest")));
            copy($src, public_path("storage/$dest"));
            $this->log("Database backup moved $src => $dest (" . calcTime($time) . ")");

            Dir::remove(dirname($src));
            return $dest;
        }

        $this->log("Database backup unsuccesful : empty database");
        return null;
    }

    public function updateHtaccess()
    {
        $file_path = $this->root_dir . '/public/.htaccess';

        if (!file_exists($file_path))
        {
            $this->log("couldn't update $file_path : file does not exist");
            return null;
        }

        $this->log("updated $file_path : RewriteRule ^(.*)$ {$this->domain}/$1 [R,L]");
        return file_put_contents($file_path, implode("\r\n", [
            '<IfModule mod_rewrite.c>',
            '   <IfModule mod_negotiation.c>',
            '       Options -MultiViews -Indexes',
            '   </IfModule>',
            '',
            '   RewriteEngine On',
            '',
            '   # Handle Authorization Header',
            '   RewriteCond %{HTTP:Authorization} .',
            '   RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]',
            '',
            '   # Redirect Trailing Slashes If Not A Folder...',
            '   RewriteCond %{REQUEST_FILENAME} !-d',
            '   RewriteCond %{REQUEST_URI} (.+)/$',
            '   RewriteRule ^ %1 [L,R=301]',
            '',
            '   # Send Requests To Front Controller...',
            '   RewriteCond %{REQUEST_FILENAME} !-d',
            '   RewriteCond %{REQUEST_FILENAME} !-f',
            '   RewriteRule ^ index.php [L]',
            '',
            config('app.live') ? '   RewriteCond %{SERVER_PORT} 80 ' : '',
            config('app.live') ? '   RewriteRule ^(.*)$ ' . $this->domain . '/$1 [R,L]' : '',
            '',
            '   # Compress HTML, CSS, JavaScript, Text, XML and fonts',
            '   AddOutputFilterByType DEFLATE application/javascript',
            '   AddOutputFilterByType DEFLATE application/rss+xml',
            '   AddOutputFilterByType DEFLATE application/vnd.ms-fontobject',
            '   AddOutputFilterByType DEFLATE application/x-font',
            '   AddOutputFilterByType DEFLATE application/x-font-opentype',
            '   AddOutputFilterByType DEFLATE application/x-font-otf',
            '   AddOutputFilterByType DEFLATE application/x-font-truetype',
            '   AddOutputFilterByType DEFLATE application/x-font-ttf',
            '   AddOutputFilterByType DEFLATE application/x-javascript',
            '   AddOutputFilterByType DEFLATE application/xhtml+xml',
            '   AddOutputFilterByType DEFLATE application/xml',
            '   AddOutputFilterByType DEFLATE font/opentype',
            '   AddOutputFilterByType DEFLATE font/otf',
            '   AddOutputFilterByType DEFLATE font/ttf',
            '   AddOutputFilterByType DEFLATE image/svg+xml',
            '   AddOutputFilterByType DEFLATE image/x-icon',
            '   AddOutputFilterByType DEFLATE text/css',
            '   AddOutputFilterByType DEFLATE text/html',
            '   AddOutputFilterByType DEFLATE text/javascript',
            '   AddOutputFilterByType DEFLATE text/plain',
            '   AddOutputFilterByType DEFLATE text/xml',
            '',
            '   # Remove browser bugs (only needed for really old browsers)',
            '   BrowserMatch ^Mozilla/4 gzip-only-text/html',
            '   BrowserMatch ^Mozilla/4\.0[678] no-gzip',
            '   BrowserMatch \bMSIE !no-gzip !gzip-only-text/html',
            '   Header append Vary User-Agent',
            '</IfModule>',
        ]));
    }

    public function updateBusinessType($type = null, $preserve_modules = false, $update_database = false)
    {
        if (!$type)
            $type = $this->business_type;

        if (!is_a($type, BusinessType::class))
            $type = BusinessType::find($type);

        if (!$type)
            throw new Exception('No query results for Business type : ' . $type);

        # update `business_type_id`
        $this->update(['business_type_id' => $type->id]);

        # copy file
        Dir::remove($this->root_dir . "/storage/app/public/business-types");
        copy(public_path('storage/' . $type->zip_path), $this->root_dir . "/storage/app/public/" . $type->zip_path);

        # insert business type
        $data = array_diff_key($type->toArray(), array_flip(['id', 'app_id']));
        $data['created_at'] = now();
        $data['updated_at'] = now();
        if (!$update_database) $data['database'] = ['option' => 'none', 'filename' => null];
        $db = $this->app_db();
        $db->insert('business_types', $data);

        # apply
        $result = $this->artisan('business-type:apply');

        # give the client the same modules he had before the applyment
        if ($update_database && $preserve_modules)
        {
            foreach ($this->configurations['modules'] as $key => $val) {
                $this->setConfig($key, $val);
            }
            $this->artisan('clear');
        }

        return $result;
    }

    public function updateLogo($image_src = null)
    {
        $client_logo_dir = $this->root_dir . "/storage/app/public/APP";
        Dir::remove($client_logo_dir);

        if ($image_src) {
            $image_name = basename($image_src);
            copy(public_path("storage/" . $image_src), "$client_logo_dir/$image_name");
        }

        $this->setConfig('logo', isset($image_name) ? "APP/$image_name" : "APP/favicon.png");
        return $this->artisan('clear');
    }

    public function updateClientInfo()
    {
        $client = $this->client;

        # logo
        $image = $client->image ?? null;
        if ($image) $this->updateLogo($image->src);

        # configurations
        $this->setEnv('APP_NAME', parseName($this->name, 'en'));
        $this->setConfig('app_name', $this->name);
        $this->setConfig('org_name', $client->name);
        $this->setConfig('slogan', $client->slogan);
        $this->setConfig('email', $client->email);
        $this->setConfig('tax_number', $client->tax_number);

        # address & commercial registration number (branches)
        $db = $this->app_db();
        if ($first_branch_id = $db->select('id', 'branches')[0]['id'] ?? null) {
            $db->update('branches', [
                'phone'             => $client->phone,
                'tax_number'        => $client->tax_number,
                'commercial_reg_no' => $client->commercial_reg_no,
                'address'           => $client->address,
                'full_address'      => $client->full_address
            ], ['id' => $first_branch_id]);
        }

        $this->artisan('clear');
    }



    // Backups

    public function backupFiles($filepath)
    {
        $this->log('Backing up files ..');
        $time = microtime(true);
        $exclusions = ['.git', '@update', '@versions', 'node_modules', 'vendor', 'storage/app/public', 'public/storage.lnk'];
        Zip::compress($this->root_dir, $filepath, $exclusions);
        $this->log('Files backup finished successfully (' . calcTime($time) . 's)');
    }

    public function backupDatabase($filepath)
    {
        $this->log('Backing up database ..');
        $time = microtime(true);
        $this->app_db()->export('*', [], $filepath);
        $this->log('Database backup finished successfully (' . calcTime($time) . 's)');
    }



    // Request

    public function prepareForValidation()
    {
        $request = new Request($this->toArray());

        $clients_root_dir = getConfig('clients_root_dir');
        $client = $this->client;

        $configurations = $request->configurations;

        $configurations['url'] = $this->domain;
        $configurations['logo'] = 'APP/favicon.png';
        $configurations['app_name'] = $client->name;
        $configurations['org_name'] = $client->name;
        $configurations['slogan'] = $client->slogan;
        $configurations['tax_number'] = $client->tax_number;
        $configurations['email'] = $client->email;

        $request->merge([
            'root_dir'          => "$clients_root_dir/" . clean(str_replace("$clients_root_dir/", "", $this->root_dir)),
            'configurations'    => $configurations,
            'version_id'        => castNull($this->version_id),
            'business_type_id'  => castNull($this->business_type_id),
            'db_database'       => clean($this->db_database),
            'db_username'       => clean($this->db_username),
        ]);

        $this->update($request->all());
        return $request;
    }

    public function validate()
    {
        return $this->prepareForValidation()->validate([

            'root_dir' => [
                'nullable',
                new UniqueExceptCurrent(RGBOnline::class, $this->id),
            ],

            'business_type_id' => [
                'required',
                'exists:business_types,id',
            ],
            'version_id' => [
                'required',
                'exists:versions,id',
            ],

            'db_driver' => [
                'required',
            ],
            'db_host' => [
                'required',
            ],
            'db_database' => [
                'required',
            ],
            'db_username' => [
                'required',
            ],
            'db_password' => [
                'required',
            ],

        ]);
    }



    // Installation

    public function install()
    {
        # prepare and validate installation request
        $this->validate();

        # select version
        $version = Version::find($this->version_id) ?? $this->app->getLatestVersion();

        #=============================================================================
        # CREATE SUBDOMAIN & COPY APP FILES ==========================================
        #=============================================================================

        $this->updateSubdomain();

        # extract project src & vendor & node_modules
        $versions_dir = getConfig('versions_root_dir');
        $files_to_extract = [
            "$versions_dir/vendor.zip",
            "$versions_dir/node_modules.zip",
            $version->path,
        ];
        foreach ($files_to_extract as $file) {
            Zip::extract($file, $this->root_dir);
            $this->log("extracted $file => {$this->root_dir}");
        }

        # create empty dirs
        $dirs_to_create = [
            'bootstrap/cache',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/testing',
            'storage/framework/views',
            'storage/app/public',
        ];
        foreach ($dirs_to_create as $dir) {
            Dir::remove($this->root_dir . '/' . $dir);
            $this->log("created dir : {$this->root_dir}/$dir");
        }

        # copy common storage dirs (app/payment-methods/delivery-companies/..)
        $dirs_to_copy = [
            'app/public/APP',
            'app/public/databases',
            'app/public/Cashier',
            'app/public/DeliveryCompanies',
            'app/public/PaymentMethods',
        ];
        foreach ($dirs_to_copy as $dir) {
            recursive_copy(storage_path($dir), $this->root_dir . "/storage/$dir");
            $this->log("copied " . storage_path($dir) . " => {$this->root_dir}/storage/$dir");
        }

        $this->updateSubdomain();

        #=============================================================================
        # DATABASE ===================================================================
        #=============================================================================

        # set database credentials & import a blank db
        $this->updateDatabaseCredentials();
        $this->updateMasterDatabaseCredentials();
        $this->importDatabase();

        #=============================================================================
        # BUSINESS TYPE INTERFACE ====================================================
        #=============================================================================

        $this->updateBusinessType($this->business_type_id);

        #=============================================================================
        # UPDATE CONFIG & ENV VARS ===================================================
        #=============================================================================

        # update configurations
        $this->log('configurations ' . json_encode($this->configurations));
        foreach ($this->configurations as $key => $value) {
            $this->setConfig($key, $value);
        }

        # set app_id & app_secret & env vars
        $this->setConfig('app_id', $this->id);
        $this->setConfig('app_secret', $this->secret);
        $this->setConfig('version', $version);
        $this->setEnv('APP_NAME', parseName($this->client->name, 'en'));

        # update client info
        $this->updateClientInfo();
        $this->updateConfigurations();

        #=============================================================================
        # SUCCESS | SET AS INSTALLED =================================================
        #=============================================================================

        $this->setConfig('installed_at', now());
    }

    public function uninstall()
    {
        $id = $this->id;
        $domain = str_replace('https://', '', $this->domain);
        $date = date("Y-m-d.H-i-s");

        $temp_dir = base_path("../temp/" . Str::random(50));
        $zip_path = base_path("../.trash/$id-$domain-$date.zip");
        Dir::remove($temp_dir);

        # backup files & database
        $this->backupFiles("$temp_dir/files_backup.zip");
        $this->backupDatabase("$temp_dir/db_backup.sql");

        # compress both backups and store it to /home/rgbksaco/RGB/.trash/{id}-{name}-{timestamp}.zip
        Zip::compressThenDelete($temp_dir, $zip_path);

        # unlicense
        $this->unlicense();

        # remove dir & contents
        Dir::remove($this->root_dir);
        $this->log('Removed dir : ' . $this->root_dir);

        # cPanel / Create database & user & give user all privileges
        if (config('app.live'))
        {
            $cpanel = new CPanel();
            $cpanel->deleteSubdomain($this->domain, $this->root_dir);
            $this->log("Deleted subdomain :" . $this->domain);
            $cpanel->deleteDatabase($this->db_database);
            $this->log("Dropped database :" . $this->db_database);
            $cpanel->deleteDatabaseUser($this->db_username);
            $this->log("Deleted database User :" . $this->db_username);
        }
        else
        {
            $this->app_db()->drop();
            $this->log("Dropped database :" . $this->db_database);
            $this->log("Deleted database User :" . $this->db_username);
        }

        # set as non-installed
        $this->update(['installed_at' => null]);
    }



    // Change version

    public function extractConfigurations()
    {
        $db = $this->app_db();

        foreach ($db->select('*', 'configuration_groups') as $group) {
            $existing_groups[$group['id']] = $group;
        }

        $existing_configurations = $db->query(" SELECT c.*, cg.configuration_group_id
                                                FROM configurations AS c
                                                JOIN configuration_group AS cg
                                                ON cg.configuration_id = c.id
                                                JOIN configuration_groups AS g
                                                ON cg.configuration_group_id = g.id
        ");

        foreach ($existing_configurations as $configuration) {
            $func = null;
            switch ($configuration['datatype']) {
                case 'boolean':
                    $func = 'boolVal';
                    break;
                case 'number':
                    $func = 'floatVal';
                    break;
                case 'array':
                    $func = fn ($value) => json_decode($value, true);
                    break;
            }
            $configurations[$existing_groups[$configuration['configuration_group_id']]['key']][$configuration['key']] = isset($func) ? $func($configuration['value']) : $configuration['value'];
        }

        return $configurations ?? null;
    }

    public function extractRolesAndPermissions()
    {
        $db = $this->app_db();

        foreach ($db->select('*', 'roles') as $role) {
            $roles[$role['id']] = $role;
            $roles[$role['id']]['employees'] = array_map(
                fn ($x) => $x['id'],
                $db->query("SELECT e.id
                            FROM employees AS e
                            JOIN employee_role AS er
                            ON er.employee_id = e.id
                            JOIN roles AS r
                            ON er.role_id = r.id
                            WHERE r.id = {$role['id']}
                ")
            );
        }

        $existing_permissions = $db->query("SELECT p.*, pr.role_id
                                            FROM permissions AS p
                                            JOIN permission_role AS pr
                                            ON pr.permission_id = p.id
                                            JOIN roles AS r
                                            ON pr.role_id = r.id
        ");

        foreach ($existing_permissions as $permission) {
            $roles[$permission['role_id']]['permissions'][] = $permission['name'];
        }

        return $roles ?? null;
    }

    public function compareDatabaseStructure(array $other_structure, $with_constraints = false)
    {
        $differenes = [];
        $this_structure = $this->app_db()->getStructureAssoc();

        foreach ($this_structure as $table => $columns) {
            foreach (array_diff_key($columns, array_flip(['CONSTRAINTS'])) as $name => $struct) {
                if (!isset($other_structure[$table][$name]) || !in_array($name, array_keys($other_structure[$table]))) {
                    $differenes[$this->database][$table][$name] = $struct ?? null;
                    if ($value = $other_structure[$table][$name] ?? null) $differenes['other'][$table][$name] = $value;
                }
            }
            if ($with_constraints) {
                foreach ($columns['CONSTRAINTS'] as $struct) {
                    if (!isset($other_structure[$table]['CONSTRAINTS']) || !in_array($struct, $other_structure[$table]['CONSTRAINTS']))
                        $differenes[$this->database][$table]['CONSTRAINTS'][] = $struct;
                }
            }
        }

        $sql = '';
        if ($current_differences = $differenes[$this->database] ?? null) {
            foreach ($current_differences as $table => $columns) {
                if (count(array_diff_key($columns, array_flip(['CONSTRAINTS'])))) {
                    $sql .= (isset($other_structure[$table]) ? "ALTER" : "CREATE") . " TABLE `$table` (\r\n";
                    $arr = array_diff_key($columns, array_flip(['CONSTRAINTS']));
                    foreach ($arr as $name => $struct) {
                        $sql .= "\t" . trim($struct, ',') . (array_keys($arr)[count($arr) - 1] == $name ? '' : ',') . "\r\n";
                    }
                    $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\r\n\r\n";
                }
                if ($constraints = $columns['CONSTRAINTS'] ?? null) {
                    foreach ($constraints as $name => $struct) {
                        $sql .= "ALTER TABLE `$table` ADD " . trim($struct, ',') . ";\r\n\r\n";
                    }
                }
            }
        }

        return $differenes;
    }

    public function updateVersion(Version $version = null)
    {
        $this->check_for_updates();

        # if no verion selected => update to latest version
        if (!$version) {
            $version = Version::find($this->app->latest_version_id);
        }

        # current version is equal or newer that needed version
        if ($this->version->id >= $version->id) {
            throw new Exception("Already on " . ($this->version->id > $version->id ? 'newer version ' : '') . "{$this->version->number} !");
        }

        $time = microtime(true);
        $global_time = $time;
        $timestamp = date('Y-m-d.H-i-s');
        $root_dir = $this->root_dir;

        $db = $this->app_db();
        $version_path = "$root_dir/@versions/{$version->number}";

        # backup files
        $files_backup = "$version_path/$timestamp-files-backup.zip";
        $this->backupFiles($files_backup);

        # backup database
        $db_backup = "$version_path/$timestamp-db-backup.sql";
        $this->backupDatabase($db_backup);

        $time = microtime(true);

        # export configuraitons to json file
        $this->log('Exporting configurations to json file ..');
        $configurations = $this->extractConfigurations();
        $configurations_json = "$version_path/$timestamp-configurations.json";
        if ($configurations) file_put_contents($configurations_json, json_encode($configurations));
        $this->log("Exported configurations to $configurations_json (" . calcTime($time) . "s)");

        # export roles & permissions to json file
        $this->log('Exporting roles & permissions to json file ..');
        $roles = $this->extractRolesAndPermissions();
        $roles_json = "$version_path/$timestamp-roles.json";
        if ($roles) file_put_contents($roles_json, json_encode($roles));
        $this->log("Exported roles & permissions to $roles_json (" . calcTime($time) . "s)");

        # trying to update system
        try {
            # delete unwanted / old files that might lead to an error
            $this->log('Deleting cache files ..');
            array_map('unlink', glob("$root_dir/{bootstrap/cache/*,config/*}", GLOB_BRACE));
            $this->log('Deleted cache files successfully (' . calcTime($time) . 's)');

            # extract new version files
            $this->log('Extracting new version files ..');
            if (Zip::extract($version->path, $root_dir)) $this->log('New version files extracted successfully (' . calcTime($time) . 's)');
            else throw new Exception("Cannot extract {$version->number} {$version->path} to $root_dir : File " . (file_exists($version->path) ? "exists" : "does not exist"));

            # migrate database
            $this->log("Migrating database ..");
            if ($migration_output = $this->artisan('migrate --force')) {
                if (count($migration_output) && $last_line = $migration_output[count($migration_output) - 1]) {
                    if (!$last_line || !in_array(explode(' ', $last_line)[0], ['Nothing', 'Migrated:'])) {
                        throw new Exception("Error while migrating database : \r\n" . implode("\r\n", $migration_output));
                    }
                }
            }
            $this->log("Migrated database (" . calcTime($time) . "s)");

            /*
                # migrate & compare old/new db structures
                
                $this->log('Updating database ..');
                $db_structure = json_decode(file_get_contents("$root_dir/@update/db_structure.json"), true);
                $before = $this->compareDatabaseStructure($db_structure, true);
                if ($migration_files = glob("$root_dir/database/migrations/versions/*.php")) {
                    foreach ($migration_files as $migration_file) {
                        $this->log("Migrating: $migration_file ..");
                        $this->artisan("migrate --path=" . str_replace($root_dir, "", $migration_file));
                        $this->log("Migrated:  $migration_file (" . calcTime($time) . "s)");
                    }
                } else $this->log("Cannot find any migration files");
                $after = $this->compareDatabaseStructure($db_structure, true);
            */

            # truncate configrations & roles tables and import update.sql file
            $this->log("Truncating configuration_group & configuration_groups & configurations & permission_role & permissions & permissions_groups & roles & actions ..");
            $db->truncate('configuration_group', 'configuration_groups', 'configurations');
            $db->truncate('permission_role', 'permissions', 'permissions_groups', 'roles', 'actions');
            $db->import("$root_dir/@update/update.sql");
            $this->log("Truncated successfully & imported $root_dir/@update/update.sql (" . calcTime($time) . "s)");

            # configurations
            $this->log("Migrating configurations ..");
            if ($configurations = $configurations ?? json_decode(file_get_contents("$version_path/configurations.json"), true)) {
                foreach ($configurations as $group_key => $values) {
                    foreach ($values as $key => $value) {
                        $db->update('configurations', ['value' => $value], ['key' => $key]);
                    }
                }
            }
            $this->log("Configurations migrated successfully (" . calcTime($time) . "s)");

            # roles & permissions
            $this->log("Migrating roles ..");
            if ($roles = $roles ?? json_decode(file_get_contents("$version_path/roles.json"), true)) {
                foreach ($roles as $role) {
                    $db->insert('roles', array_diff_key($role, array_flip(['permissions', 'employees'])));
                    foreach ($role['permissions'] ?? [] as $permission_name) {
                        if (!($permission_id = $db->select('id', 'permissions', ['name' => $permission_name])[0]['id'] ?? null))
                            throw new Exception("Cannot find permission with name : $permission_name");

                        $db->insert('permission_role', [
                            'permission_id' => $permission_id,
                            'role_id'       => $role['id'],
                        ]);
                    }
                    foreach ($role['employees'] ?? [] as $employee_id) {
                        $db->insert('employee_role', [
                            'role_id'       => $role['id'],
                            'employee_id'   => $employee_id,
                        ]);
                    }
                }
            }
            $this->log("roles migrated successfully (" . calcTime($time) . "s)");

            # post-update artisan commands
            $this->artisan('config:cache');
            $this->artisan('locales:update');
            $this->artisan('clear');
        }

        # update failed => rollback changes
        catch (Exception $e) {
            $this->log("Update Failed. Rolling back changes on files & database. (" . calcTime($time) . "s)\r\n" . $e->getMessage());
            
            # delete unwanted / old files that might lead to an error
            $this->log('Deleting cache files ..');
            array_map('unlink', glob("$root_dir/{bootstrap/cache/*,config/*}", GLOB_BRACE));
            $this->log('Deleted cache files successfully (' . calcTime($time) . 's)');

            # restore old system files
            $this->log('Restoring files ..');
            Zip::extract($files_backup, $this->root_dir);
            $this->log("Restored files from $files_backup (" . calcTime($time) . "s)");

            # import last database backuo
            $this->log('Restoring database ..');
            $db->import($db_backup);
            $this->log("Restored database from $db_backup (" . calcTime($time) . "s)");

            throw $e;
        }

        # successfully updated system
        $this->log("Successfully updated version {$this->version_number} to {$version->number} (" . calcTime($global_time) . "s)");
        $this->update(['version_id' => $version->id]);
        $this->setConfig('version', $version->toArray());

        return true;
    }



    // License

    public function license()
    {
        $this->start_process('license');
        $this->setConfig('demo', false);
        $this->setConfig('licensed_at', now());
        $this->update(['licensed_at' => now()]);
        $this->end_process('license', true);
    }

    public function unlicense()
    {
        $this->start_process('unlicense');
        $this->setConfig('demo', true);
        $this->setConfig('licensed_at', null);
        $this->update(['licensed_at' => null]);
        $this->artisan('clear');
        $this->end_process('unlicense', true);
    }



    // helper functions

    public function artisan($cmd)
    {
        $php = config('app.live') ? '/usr/local/bin/php' : 'C:/xampp/php/php.exe';
        $command = "$php {$this->root_dir}/artisan $cmd";
        exec($command, $output);
        $this->log("command $command : " . implode("\r\n", $output));
        return $output;
    }

    // configurations

    public function getConfigModel($key)
    {
        $db = $this->app_db();

        # group
        $group = $db->select('*', 'configuration_groups', ['key' => $key])[0] ?? null;
        if ($group) return array_merge($group, ['$type$' => 'group']);

        # config
        $config = $db->select('*', 'configurations', ['key' => $key])[0] ?? null;
        if ($config) return array_merge($config, ['$type$' => 'config']);

        throw new Exception("cannot find config : $key");

        return null;
    }
    public function getConfigCast($config)
    {
        $func = null;
        $value = $config['value'];
        switch ($config['datatype'])
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
    public function getConfig($key)
    {
        $db = $this->app_db();

        # group
        $group = $db->select('*', 'configuration_groups', ['key' =>  $key])[0] ?? null;
        if ($group)
        {
            $config = null;

            $rows = $db->query(
                "SELECT C.* FROM `configurations` C

                JOIN `configuration_group` CG
                ON C.id = CG.configuration_id
                
                JOIN `configuration_groups` G
                ON G.id = CG.configuration_group_id
                
                WHERE G.`key` = '$key';"
            );

            foreach ($rows as $row) {
                $config[$row['key']] = $this->getConfigCast($row);
            }

            return $config;
        }

        # config
        $config = $db->select('*', 'configurations', ['key' =>  $key])[0] ?? null;
        if ($config)
        {
            return $this->getConfigCast($config);
        }

        return null;
    }

    public function setConfigCast($config)
    {
        $func = null;
        $value = $config['value'];

        if (is_null($value))
            return null;

        switch ($config['datatype'])
        {
            case 'timestamp':
                $func = function ($val) { return date("Y-m-d\TH:i:s\Z", strtotime($val)); };
                break;
            case 'boolean':
                $func = function ($x) { return $x ? '1' : '0'; };
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

        return $func ? $func($value) : $value;
    }
    public function setConfig($key, $value)
    {
        $config = $this->getConfigModel($key);
        $db = $this->app_db();

        if ($config)
        {
            # config
            if ($config['$type$'] == 'config')
            {
                $value = $this->setConfigCast(array_merge($config, ['value' => $value]));
                $out = $db->update('configurations', ['value' => $value], ['key' => $key]);
                $this->log("set config : $key => $value (" . ($out ? 'success' : 'failed') . ")");
            }

            # group
            else
            {
                foreach ($value as $k => $v)
                {
                    $v = $this->setConfigCast(array_merge($this->getConfigModel($k), ['value' => $v]));
                    $out = $db->update('configurations', ['value' => $v], ['key' => $k]);
                    $this->log("set config : $k => $v (" . ($out ? 'success' : 'failed') . ")");
                }
            }
        }

        return $this->getConfig($key);
    }

    public function fetchConfigurations()
    {
        foreach ($this->app_db()->select('`key`, `value`, `datatype`', 'configurations') as $config)
        {
            $configurations[$config['key']] = $this->getConfigCast($config);
        }
        return $configurations ?? [];
    }
    public function updateConfigurations()
    {
        $configurations = $this->fetchConfigurations();
        $this->update(['configurations' => $configurations]);
        $this->artisan('clear');
        $this->log('updated configurations');
        return $configurations;
    }

    // env

    public function getEnv($key)
    {
        $env_path = "{$this->root_dir}/.env";

        if (!file_exists($env_path))
            return null;

        $contents = explode("\r\n", file_get_contents($env_path));
        $match = [...array_filter($contents, fn ($line) => startsWith($line, "$key="))][0] ?? null;
        return $match ? (explode('=', $match)[1] ?? null) : null;
    }
    public function setEnv($key, $value)
    {
        $env_path = "{$this->root_dir}/.env";

        if (!file_exists($env_path))
            return false;

        $contents = explode("\r\n", file_get_contents($env_path));

        foreach ($contents as $i => $line) {
            $data = $line ? explode('=', $line) : $line;

            if ($data && $data[0] == $key) {
                $contents[$i] = implode('=', [$data[0], str_contains($value, ' ') ? '"' . $value . '"' : $value]);
                $updated = !!file_put_contents($env_path, implode("\r\n", $contents));
                $this->log("update env var : {$this->root_dir}/.env $key => $value (" . ($updated ? 'successs' : 'failed') . ")");
                return $updated;
            }
        }

        return false;
    }
}
