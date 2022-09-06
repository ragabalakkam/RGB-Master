<?php

# vars

$hasVendor = false; // !!!(isset($_GET['no_vendor']));
$hasNodeModules = false; // !!!(isset($_GET['no_node_modules']));

$date = date('d-m-Y', time());

$xampp_dir = 'C:/xampp/htdocs/rgbksa';
$project_name = "master";

$project_dir = "$xampp_dir/$project_name";
$desktop_dir = "C:/Users/" . getWindowsUser() . "/Desktop";
$temp_dir = "$xampp_dir/" . generate_random_string(30);

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# helper functions

function getWindowsUser()
{
  $users = [...array_diff(scandir(__DIR__ . '..\..\..\..\..\..\..\Users'), ['.', '..', 'All Users', 'Default', 'Public', 'desktop.ini'])];
  return $users[count($users) - 1];
}

function scroll_to_top()
{
  echo '
    <script>
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    </script>
  ';
}

function scroll_to_bottom()
{
  echo '
    <script>
      window.scrollTo(0,document.body.scrollHeight);
    </script>
  ';
}

function create_url_file($name, $link, $favicon = 'C:\xampp\htdocs\rgbksa\master\public\favicon.ico')
{
  file_put_contents("$name.url", implode("\r\n", [
    "[InternetShortcut]",
    "URL=$link",
    "IconFile=$favicon",
    "IconIndex=0",
  ]));
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=