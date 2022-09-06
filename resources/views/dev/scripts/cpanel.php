<?php

use App\Helpers\Classes\CPanel;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function delete_database_and_user($name)
{
  $cpanel = new CPanel();
  $cpanel->deleteDatabase($name);
  $cpanel->deleteDatabaseUser($name);
}

function create_database_and_user($name, $password = 'afaqrgb2000')
{
  $name = "rgbksaco_" . str_replace('rgbksaco_', '', $name);
  $cpanel = new CPanel();
  $cpanel->createDatabase($name);
  $cpanel->createDatabaseUser($name, $password);
  $cpanel->setAllPrivilegesOnDatabase($name, $name);
}
