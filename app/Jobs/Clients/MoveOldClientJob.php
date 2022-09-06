<?php

namespace App\Jobs\Clients;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\API\Clients\ClientsController;
use App\Http\Requests\Clients\ClientRequest;
use App\Models\Apps\RGBOnline;
use App\Models\Master\BusinessType;
use App\Models\Versions\Version;
use Exception;
use Illuminate\Support\Facades\Log;

class MoveOldClientJob implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $app_client;
	
	public $tries = 1;
	public $timeout = 86400;
	public $failOnTimeout = true;
	public $deleteWhenMissingModels = true;

    private $old_name;
    private $new_name;
    private $blank_database;

	public function __construct($old_name, $new_name, $blank_database = '/home/rgbksaco/RGB/sql-files/blank.sql')
	{
        $this->old_name = $old_name;
        $this->new_name = $new_name;
        $this->blank_database = $blank_database;
	}

	public function handle()
	{
        $old_name = $this->old_name;
        $new_name = $this->new_name;
        $blank_database = $this->blank_database;

        #=== loading .env file ===========================================================================================================================

        $path = "/home/rgbksaco/rgbksa/$old_name/.env";
        
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        if (!is_readable($path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $path));
        }

        $dotenv = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0)
                continue;

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $dotenv))
            {
                putenv(sprintf('%s=%s', $name, $value));
                $dotenv[$name] = $value;
            }
        }
  
        if (!isset($dotenv['DB_DATABASE'])) {
            throw new \InvalidArgumentException(sprintf('Undefined index %s', "DB_DATABASE"));
        }
        
        #=== creating helper apps ========================================================================================================================
        
        if ($old_app = RGBOnline::where('root_dir', "/home/rgbksaco/rgbksa/$old_name")->first())
        {
            $old_app->delete();
        }

        $data_in_common = [
            'client_id'   => 37,
            'version_id'  => 11,
            'app_id'      => 1,
            'secret'      => generate_random_string(50),
        ];

        $old_app = RGBOnline::create(array_merge($data_in_common, [
            'id'          => time() . generate_random_string(5),
            'name'        => localize([$old_name, $old_name]),
            'root_dir'    => "/home/rgbksaco/rgbksa/$old_name",
            'db_database' => $dotenv['DB_DATABASE'],
            'db_username' => $dotenv['DB_USERNAME'],
            'db_password' => $dotenv['DB_PASSWORD'],
        ]));

        $tmp_app = RGBOnline::create(array_merge($data_in_common, [
            'id'          => time() . generate_random_string(5),
            'name'        => localize(['temp', 'temp']),
            'root_dir'    => "/home/rgbksaco/RGB/temp",
            'db_database' => "rgbksaco_temp",
            'db_username' => "rgbksaco_temp",
            'db_password' => "afaqrgb2000",
        ]));

        #=== copy files ==================================================================================================================================

        $clients_root_dir = getConfig('clients_root_dir');
        foreach ($old_app->app_db()->select('*', 'images') as $image)
        {
            $src = "{$old_app->root_dir}/storage/app/public/{$image['src']}";
            $dst = "$clients_root_dir/$new_name/storage/app/public/{$image['src']}";
            create_dir_if_not_exist(dirname($dst));
            if (file_exists($src) && !is_dir($src)) copy($src, $dst);
        }

        #=== import blank database ==========================================================================================================
        
        $tmp_db = $tmp_app->app_db();
        $tmp_db->empty();
        $tmp_db->import($blank_database);

        #=== import data from old client database ========================================================================================================

        $old_db = $old_app->app_db();
        $db_dest = "/home/rgbksaco/RGB/sql-files/rgbksaco_$old_name.sql";

        create_dir_if_not_exist(dirname($db_dest));
        foreach([
            'accounts,branches,points_of_sale,point_of_sale_warehouse,fees,branch_fee,branch_preparation_points,delivery_companies,dining_areas,dining_tables,images',
            'countries,states,cities,districts,users,payment_methods,suppliers,supplier_warehouse,units,states_of_matter,departments,employees,shifts',
            'main_categories,sub_categories,sizes,products,product_variations,ingredients,pricing_systems_product_variations,product_variations_ingredients',
            'general_daily,general_daily_entries,general_daily_presets,general_daily_preset_entries',
            'invoices,invoices_products,invoices_payments',
            'event_logs',
            'warehouses,warehouse_movements,inventories,read_stores',
            'disbursements,disbursement_movements,disbursements_details,disbursements_authorizations',
        ] as $key => $tables)
        {
            $path = dirname($db_dest) . "/temp/$key-" . basename($db_dest);
            $old_db->export($tables, [], $path, ['create_tables' => false]);
            $tmp_db->import($path);
        }
        remove_dir(dirname($db_dest) . '/temp');

        #=== configurations ==============================================================================================================================

        $config_group_keys = [
            'app',
            'modules',
            'organization',
            'sales_settings',
            'cashier_settings',
        ];
        foreach ($config_group_keys as $group_key) {
            foreach($old_app->getConfig($group_key) as $key => $val) {
            if ($tmp_app->getConfig($key)) {
                $tmp_app->setConfig($key, $val);
            }
            }
        }
        $tmp_app->setConfig('live', 1);

        #=== roles & permissions =========================================================================================================================

        $roles_map = [];
        
        foreach (['Super Admin', 'Admin', 'Cashier', 'Shifts manager'] as $role_name)
        {
            $old_id = $tmp_db->query("SELECT * FROM `roles` WHERE `name` LIKE '%\"$role_name%'")[0]['id'] ?? 9;
            $new_id = $old_db->query("SELECT * FROM `roles` WHERE `name` LIKE '%\"$role_name%'")[0]['id'] ?? 9;
            $roles_map[$new_id] = $old_id;
        }
        
        foreach ($old_db->select('*', 'employee_role') as $employee_role)
        {
            $tmp_db->insert('employee_role', [
            'employee_id' => $employee_role['employee_id'],
            'role_id'     => $roles_map[$employee_role['role_id']],
            ]);
        }

        #=== export new client database ==================================================================================================================

        exec("mysqldump -u {$tmp_app->db_username} -p{$tmp_app->db_password} {$tmp_app->db_database} > /home/rgbksaco/RGB/sql-files/{$old_app->db_database}.sql");
        
        #=== delete tmp & old apps =======================================================================================================================

        $old_app->delete();
        $tmp_app->delete();
        
        #=== create client ===============================================================================================================================

        $business_type = BusinessType::orderBy('id', 'desc')->first();
        $version = Version::orderBy('id', 'desc')->first();
        $last_branch = $old_db->select('phone,address', 'branches')[0];
        
        app(ClientsController::class)->store(new ClientRequest([
            'name'        => $old_app->getconfig('org_name'),
            'slogan'      => $old_app->getconfig('slogan'),
            'email'       => $old_app->getconfig('email'),
            'tax_number'  => $old_app->getconfig('tax_number'),
            'address'     => json_decode($last_branch['address'], true),
            'phone'       => $last_branch['phone'],
            'apps'        => [
            [
                'on'                => true,
                'app_id'            => 1,
        
                'version_id'        => $version->id,
                'business_type_id'  => $business_type->id,
        
                'domain'            => "https://$new_name.rgbksa.com",
                'root_dir'          =>  "$clients_root_dir/$new_name",
        
                'db_driver'         => 'mysql',
                'db_host'           => 'localhost',
                'db_username'       => "rgbksaco_rgb_$new_name",
                'db_database'       => "rgbksaco_rgb_$new_name",
                'db_password'       => 'afaqrgb2000',
        
                'configurations'    => [
                "app"               => [
                    "app_id"      => null,
                    "app_secret"  => null,
                    "demo"        => false,
                    "offline"     => false,
                    "live"        => true
                ],
                "organization"      => [
                    "number_of_branches"        => $old_app->getConfig('number_of_branches'),
                    "number_of_points_of_sale"  => $old_app->getConfig('number_of_points_of_sale'),
                ],
                "modules"           => $old_app->getConfig('modules'),
                "cashier_settings"  => $old_app->getConfig('cashier_settings'),
                ],
            ],
            ],
        ]));
    }

    public function failed(Exception $exception)
    {
        Log::alert(str_repeat('new', 100));
        Log::info((string) $exception);
    }
}
