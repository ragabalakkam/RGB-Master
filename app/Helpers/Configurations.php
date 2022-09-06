<?php

use App\Models\Configurations\Group;
use App\Models\Configuration;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function getConfigModel($key)
{
  return Group::where('key', $key)->first() ?? Configuration::where('key', $key)->first() ?? null;
}

function getConfig($key)
{
  return getConfigModel($key)->value ?? null;
}

function setConfig($key, $value, $datatype = 'string')
{
  $config = getConfigModel($key);
  $config->update(['value' => $value]);
  return $config;
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function isOffline()
{
  return getConfig('offline');
}

function getGoogleApiKey()
{
  return getConfig('google_api_key');
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function hasModule($name)
{
  try {
    return getConfig('modules')[$name];
  } catch (Exception $e) { return false; }
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# .env & config

function get_env_var($key)
{
  $env_path = base_path('.env');

  if (!file_exists($env_path))
    return false;

  $contents = explode("\r\n", file_get_contents($env_path));
  $match = [...array_filter($contents, fn ($line) => startsWith($line, "$key="))][0] ?? null;
  return $match ? explode('=', $match)[1] : null;
}

function update_env_var($client_root_dir, $key, $value)
{
  $env_path = "$client_root_dir/.env";

  if (!file_exists($env_path))
    return false;

  $contents = explode("\r\n", file_get_contents($env_path));

  foreach ($contents as $i => $line) {
    $data = $line ? explode('=', $line) : $line;

    if ($data && $data[0] == $key) {
      $contents[$i] = implode('=', [$data[0], $value ? (str_contains($value, ' ') ? '"' . $value . '"' : $value) : '']);
      return !!file_put_contents($env_path, implode("\r\n", $contents));
    }
  }

  return false;
}