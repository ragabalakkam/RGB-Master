<?php

$values = [
  'reset'     => env('RESET_PATH',  'reset'),
  'backup'    => env('BACKUP_PATH', 'backup'),
  // 'cache'     => (config('app.live') ? '../public_html' : 'public') . '/' . env('CACHE_PATH',  'cache'),
  // 'theme'     => (config('app.live') ? '../public_html' : 'public') . '/' . 'css',
  'cache'     => 'public/' . env('CACHE_PATH',  'cache'),
  'theme'     => 'public/css',
];

$modes = [
  'backup',
  'reset',
  'cache'
];

$routes_with_reset_and_backup = [
  'locales'   => 'lang',
  'locations' => '',
  'configurations' => '',
];

foreach ($modes as $mode) {
  foreach ($routes_with_reset_and_backup as $key => $value) {
    $values[$key . "_$mode"]  = $values[$mode]  . "\\$value";
  }
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#

foreach ($values as $key => $value) {
  $values[$key] = forward_slashes($value, DIRECTORY_SEPARATOR);
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#

return $values;
