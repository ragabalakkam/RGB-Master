<?php

# facades
use App\Helpers\Classes\MysqlDB;

# models
use App\Models\Apps\RGBOnline;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function outdated_dbs_by_tables_count($count = 83)
{
  foreach (RGBOnline::installed()->get() as $app) {
    if (count((new MysqlDB($app->db_database, $app->db_username, $app->db_password))->query('SHOW TABLES')) != $count)
      $apps[] = $app->db_database;
  }
  return $apps ?? [];
}

function outdated_dbs_by_column($table, $column)
{
  foreach (RGBOnline::installed()->get() as $app) {
    $db = new MysqlDB($app->db_database, $app->db_username, $app->db_password);
    if (!count($db->query("SHOW COLUMNS FROM `$table` LIKE '$column'"))) $apps[] = $app->db_database;
  }
  return $apps ?? [];
}
