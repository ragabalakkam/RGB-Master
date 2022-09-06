<?php

# controllers
use App\Http\Controllers\API\Clients\ClientsController;

# requests
use App\Http\Requests\Clients\ClientRequest;

# models
use App\Models\Clients\Client;
use App\Models\Versions\Version;
use App\Models\Master\BusinessType;
use App\Models\Apps\RGBOnline;

# facades
use Dotenv\Dotenv;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function get_latest_client()
{
  return Client::orderBy('created_at', 'desc')->first();
}

function create_client($name = null)
{
  $live = getConfig('live');
  $name = $name ?? $name = 'test' . time();

  $business_type = BusinessType::orderBy('id', 'desc')->first();

  app(ClientsController::class)->store(new ClientRequest([
    'name' => [
      'en' => 'Fahad A. S. Baokbah Trading Est.',
      'ar' => 'مؤسسة فهد علي سالم باعقبة التجارية'
    ],
    'slogan' => [
      'en' => 'For selling all sewing machines & spare parts',
      'ar' => 'لبيع جميع ماكينات الخياطة وقطع غيارها',
    ],
    'apps' => [
      [
        'on'                => true,
        'app_id'            => 1,

        'version_id'        => Version::orderBy('id', 'desc')->first()->id,
        'business_type_id'  => $business_type->id,

        'domain'            => $live ? "https://$name.rgbksa.com" : "http://$name.rgbksa.io",
        'root_dir'          => getConfig('clients_root_dir') . "/$name",

        'db_driver'         => 'mysql',
        'db_host'           => 'localhost',
        'db_username'       => "rgbksaco_rgb_$name",
        'db_database'       => "rgbksaco_rgb_$name",
        'db_password'       => 'afaqrgb2000',

        'configurations'    => [
          "app"               => [
            "app_id"      => null,
            "app_secret"  => null,
            "demo"        => true,
            "offline"     => false,
            "live"        => true
          ],
          "organization"      => [
            "number_of_branches"        => 1,
            "number_of_points_of_sale"  => 1,
          ],
          "modules"           => $business_type->modules,
          "cashier_settings"  => $business_type->cashier_settings,
        ],
      ],
    ],
  ]));
}

function create_client_from_domain($old_name, $new_name, $blank_database = '/home/rgbksaco/RGB/sql-files/blank.sql')
{
  #=== creating helper apps ==============================================================================================================================

  $env_dir = "/home/rgbksaco/rgbksa/$old_name";

  if (!file_exists("$env_dir/.env")) {
    throw new \InvalidArgumentException(sprintf('%s does not exist', "$env_dir/.env"));
  }

  $dotenv = Dotenv::createImmutable($env_dir);
  $dotenv = $dotenv->load();

  if (!isset($dotenv['DB_DATABASE'])) {
    throw new \InvalidArgumentException(sprintf('Undefined index %s', "DB_DATABASE"));
  }

  #=== creating helper apps ==============================================================================================================================

  if ($old_app = RGBOnline::where('root_dir', "/home/rgbksaco/rgbksa/$old_name")->first()) {
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

  #=== copy files ========================================================================================================================================

  $clients_root_dir = getConfig('clients_root_dir');
  foreach ($old_app->app_db()->select('*', 'images') as $image) {
    $src = "{$old_app->root_dir}/storage/app/public/{$image['src']}";
    $dst = "$clients_root_dir/$new_name/storage/app/public/{$image['src']}";
    create_dir_if_not_exist(dirname($dst));
    if (file_exists($src) && !is_dir($src)) copy($src, $dst);
  }

  #=== import blank database =============================================================================================================================

  $tmp_db = $tmp_app->app_db();
  $tmp_db->empty();
  $tmp_db->import($blank_database);

  #=== import data from old client database ==============================================================================================================

  $old_db = $old_app->app_db();
  $db_dest = "/home/rgbksaco/RGB/rgbksaco_$old_name.sql";

  create_dir_if_not_exist(dirname($db_dest));
  foreach ([
    'accounts,branches,points_of_sale,point_of_sale_warehouse,fees,branch_fee,branch_preparation_points,delivery_companies,dining_areas,dining_tables,images',
    'countries,states,cities,districts,users,payment_methods,suppliers,supplier_warehouse,units,states_of_matter,departments,employees,shifts',
    'main_categories,sub_categories,sizes,products,product_variations,ingredients,pricing_systems_product_variations,product_variations_ingredients',
    'general_daily,general_daily_entries,general_daily_presets,general_daily_preset_entries',
    'invoices,invoices_products,invoices_payments',
    'event_logs',
    'warehouses,warehouse_movements,inventories,read_stores',
    'disbursements,disbursement_movements,disbursements_details,disbursements_authorizations',
  ] as $key => $tables) {
    $path = dirname($db_dest) . "/temp/$key-" . basename($db_dest);
    $old_db->export($tables, [], $path, ['create_tables' => false]);
    $tmp_db->import($path);
  }
  remove_dir(dirname($db_dest) . '/temp');

  #=== configurations ====================================================================================================================================

  $config_group_keys = [
    'app',
    'modules',
    'organization',
    'sales_settings',
    'cashier_settings',
  ];
  foreach ($config_group_keys as $group_key) {
    foreach ($old_app->getConfig($group_key) as $key => $val) {
      if ($tmp_app->getConfig($key)) {
        $tmp_app->setConfig($key, $val);
      }
    }
  }
  $tmp_app->setConfig('live', 1);

  #=== roles & permissions ===============================================================================================================================

  $roles_map = [];

  foreach (['Super Admin', 'Admin', 'Cashier', 'Shifts manager'] as $role_name) {
    $old_id = $tmp_db->query("SELECT * FROM `roles` WHERE `name` LIKE '%\"$role_name%'")[0]['id'] ?? 9;
    $new_id = $old_db->query("SELECT * FROM `roles` WHERE `name` LIKE '%\"$role_name%'")[0]['id'] ?? 9;
    $roles_map[$new_id] = $old_id;
  }

  foreach ($old_db->select('*', 'employee_role') as $employee_role) {
    $tmp_db->insert('employee_role', [
      'employee_id' => $employee_role['employee_id'],
      'role_id'     => $roles_map[$employee_role['role_id']],
    ]);
  }

  #=== export new client database ========================================================================================================================

  exec("mysqldump -u {$tmp_app->db_username} -p{$tmp_app->db_password} {$tmp_app->db_database} > /home/rgbksaco/RGB/sql-files/new_{$old_app->db_database}.sql");

  #=== delete tmp & old apps =============================================================================================================================

  $old_app->delete();
  $tmp_app->delete();

  #=== create client =====================================================================================================================================

  $business_type = BusinessType::orderBy('id', 'desc')->first();
  $version = Version::orderBy('id', 'desc')->first();
  $last_branch = $old_db->select('phone,address', 'branches')[0];

  return app(ClientsController::class)->store(new ClientRequest([
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
  ]))->original;
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# Listing

function list_old_clients()
{
  return [
    ...array_filter(
      scandir_clear('/home/rgbksaco/rgbksa'),
      fn ($dir) => is_dir("/home/rgbksaco/rgbksa/$dir") && file_exists("/home/rgbksaco/rgbksa/$dir/.env") && $dir != 'socialcafee'
    )
  ];
}

function list_new_clients()
{
  $clients_root_dir = getConfig('clients_root_dir');
  return [...array_filter(scandir_clear($clients_root_dir), fn ($dir) => startsWith($dir, 'new'))];
}

function list_empty_clients()
{
  return [...array_filter(scandir_clear("/home/rgbksaco/RGB/clients"), fn ($dir) => is_dir($dir) && !file_exists("/home/rgbksaco/RGB/clients/$dir/.env"))];
}

function list_duplicates()
{
  $unique_names = [];
  foreach (RGBOnline::all() as $app) {
    $name = parseName($app->name);
    if (!in_array($name, $unique_names)) $unique_names[] = $name;
    else $duplicated_names[] = $name;
  }
  return $duplicated_names;
}

function list_outdated_clients()
{
  $ids = RGBOnline::installed()->get()->filter(fn ($app) => $app->app->latest_version_id != $app->version_id)->map(fn ($app) => $app->client_id);
  foreach ($ids as $id) {
    $results[] = "https://master.rgbksa.com/master/clients/$id";
  }
  foreach ($results ?? [] as $result) {
    echo "<p><a target='_blank' href='$result'>$result</a></p>";
  }
  return $results ?? [];
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# UPDATE CLIENT (need to be scheduled to automatically update clients in master)

function update_client($app, $only_print = false)
{
  $db = $app->app_db();
  $data = [
    'name'              => $app->getConfig('org_name'),
    'slogan'            => $app->getConfig('slogan'),

    'tax_number'        => $db->select('value', 'configurations', ['key' => 'tax_number'])[0]['value'] ?? null,
    'commercial_reg_no' => $db->select('commercial_reg_no', 'branches')[0]['commercial_reg_no'] ?? null,
    'email'             => $db->select('value', 'configurations', ['key' => 'email'])[0]['value'] ?? null,
    'phone'             => $db->select('phone', 'branches')[0]['phone'] ?? null,

    'address'           => json_decode($db->select('address', 'branches')[0]['address'], true) ?? null,
    'full_address'      => json_decode($db->select('full_address', 'branches')[0]['full_address'], true) ?? null,
  ];
  return $only_print ? $data : $app->client->update($data);
}

function update_client_by_dir($dir, $only_print = false)
{
  return update_client(RGBOnline::where("root_dir", "/home/rgbksaco/RGB/clients/$dir")->first(), $only_print);
}

function update_client_by_db($databse, $only_print = false)
{
  return update_client(RGBOnline::where("db_database", "rgbksaco_$databse")->first(), $only_print);
}

function update_all_clients($only_print = false)
{
  foreach (RGBOnline::all() as $app) {
    $results[] = update_client($app, $only_print);
  }
  return $results ?? [];
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function list_domains()
{
  echo '<p style="white-space: pre-wrap">' . implode("\r\n", RGBOnline::where('domain', 'not like', "%.%.com")->pluck('domain')->toArray()) . '</p>';
}

function list_subdomains($main_domain = 'rgbksa')
{
  echo '<p style="white-space: pre-wrap">' . implode("\r\n", RGBOnline::where('domain', 'like', "%.$main_domain.com")->pluck('domain')->toArray()) . '</p>';
}

