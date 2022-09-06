<?php

use App\Models\Apps\RGBOnline;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function scan_files($delete = false)
{
  $files = glob('/home/rgbksaco/{*,*/*,*/*/*,*/*/*/*}/{upl,option,lib}.php', GLOB_BRACE);
  $files = array_diff($files, ['/home/rgbksaco/rgb-company.com/wp-includes/option.php']);
  return $delete ? array_map('unlink', $files) : $files;
}

function copy_file_from_client_to_others($app_dir, $file_path)
{
  foreach (RGBOnline::installed()->get() as $app) {
    copy("$app_dir/$file_path", "{$app->root_dir}/$file_path");
  }
}

function update_client_files($client_dir, $zip_path)
{
  $clients_root_dir = getConfig('clients_root_dir');

  if (!file_exists("$clients_root_dir/$zip_path"))
    throw new Exception("No such file or directory $clients_root_dir/$zip_path");

  return extract_zip("$clients_root_dir/$zip_path", "$clients_root_dir/$client_dir");
}

function update_all_clients_files($zip_path)
{
  $clients_root_dir = getConfig('clients_root_dir');

  if (!file_exists("$clients_root_dir/$zip_path"))
    throw new Exception("No such file or directory $zip_path");

  foreach_app(fn ($app) => $app && $app->installed_at ? update_client_files(str_replace('/home/rgbksaco/RGB/clients/', '', $app->root_dir), $zip_path) : null);
}