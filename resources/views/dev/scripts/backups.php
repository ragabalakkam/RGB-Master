<?php

use Illuminate\Support\Facades\Http;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function get_backups($domain = 'https://master.rgbksa.com')
{
  $response = Http::post("$domain/api/v1/auth/login", [
    'username' => 'rgbsupport',
    'password' => 'PASS#WORD',
  ]);
  $token = explode('=', $response->headers()['Set-Cookie'][0], 2)[1];

  $response = Http::withToken($token)->get("$domain/api/v1/database/backups");
  return $response->json() ?? $response->body();
}

function backup_all_apps()
{
  foreach_installed_app(
    fn ($app) => exec("mysqldump -u {$app->db_username} -p{$app->db_password} {$app->db_database} > /home/rgbksaco/RGB/{$app->db_database}.sql")
  );
}
