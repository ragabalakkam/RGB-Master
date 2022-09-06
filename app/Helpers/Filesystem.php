<?php

function getSystemUser()
{
  $users = [...array_diff(scandir(__DIR__ . '..\..\..\..\..\..\..\Users'), ['.', '..', 'All Users', 'Default', 'Public', 'desktop.ini'])];
  return $users[count($users) - 1] ?? null;
}

function getDesktopPath()
{
  $system_user = getSystemUser();
  return $system_user ? "C:/Users/$system_user/Desktop" : null;
}

function x_copy($source, $destination)
{
  create_dir_if_not_exist(dirname($destination));
  return copy($source, $destination);
}

function x_file_put_contents(...$args)
{
  create_dir_if_not_exist(dirname($args[0]));
  return file_put_contents(...$args);
}
