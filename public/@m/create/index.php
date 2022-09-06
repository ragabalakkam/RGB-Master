<?php

require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../autoload.php';

ob_start();

create_dir_if_not_exist($temp_dir);

$to_copy = [
  '@factory-reset'  => [],
  'app'             => [],
  'config'          => [],
  'routes'          => [],
  'bootstrap'       => ['cache'],
  'resources'       => ['css', 'sass', 'js'],
  'public'          => [
    'plugins',
    '@m',
    'storage',
    'js/app.js.LICENSE.txt',
    'js/dev.js.LICENSE.txt',
    'js/auth.js.LICENSE.txt',
    'js/master.js.LICENSE.txt',
    'js/fontawesome.min.js',
  ],
  'storage'         => [
    'app/public',
    'framework/cache',
    'framework/sessions',
    'framework/views',
    'logs/laravel.log'
  ],
  '.env'            => [],
  '.styleci.yml'    => [],
  'artisan'         => [],
  'phpunit.xml'     => [],
  'server.php'      => [],
  'composer.json'   => [],
];

$to_create = [
  'bootstrap/cache',
  'storage/framework/cache',
  'storage/framework/sessions',
  'storage/framework/views',
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RGB Master | Create</title>

  <link rel="shortcut icon" href="'../../favicon.ico" />
  <link rel="stylesheet" href="../../css/theme.css" />
  <link rel="stylesheet" href="../../css/app.css" />
  <link rel="stylesheet" href="../../css/fontawesome.css" />
</head>

<body>

  <?php

  echo '<p>running npm run prod</p>';
  exec('npm run prod', $output);
  echo implode("<br/>", $output);

  foreach ($to_copy as $file => $except) {
    $file_path = "$project_dir/$file";
    echo '
      <div class="card">
        <div class="card-head p-3 h3">' . $file . '</div>
        <div class="card-body p-3">
    ';

    if (is_file($file_path)) {
      copy($file_path, "$temp_dir/$file");
      echo "<p class='px-3'>copied : $project_dir/$file</p>";
    } else recursive_copy($file_path, "$temp_dir/$file", $except, true);

    echo '
        </div>
      </div>
    ';
  }

  foreach ($to_create as $file) {
    create_dir_if_not_exist("$temp_dir/$file");
    echo "<p class='px-3'>created : $temp_dir/$file</p>";
  }

  # Vendor
  if ($hasVendor) {
    echo '<p class="px-3 fs-5 mt-3">Zipping vendor ..</p>';
    scroll_to_bottom();
    echo '<p class="px-3 my-3">zipped in ' . round(get_execution_time(function () use ($project_dir, $temp_dir) {
      return recursive_copy("$project_dir/vendor", "$temp_dir/vendor", [], true);
    }), 2) . ' seconds</p>';
    scroll_to_bottom();
  }

  # Node Modules
  if ($hasNodeModules) {
    echo '<p class="px-3 fs-5">Zipping node_modules ..</p>';
    scroll_to_bottom();
    echo '<p class="px-3">zipped in ' . round(get_execution_time(function () use ($project_dir, $temp_dir) {
      return recursive_copy("$project_dir/node_modules", "$temp_dir/node_modules", [], true);
    }), 2) . ' seconds</p>';
    scroll_to_bottom();
  }

  ?>

  <p class="m-3 p-2 mb-5 bg-success text-light rounded fs-6">
    <?php

    # zip file for offline
    echo 'Zip : ' . (zip($temp_dir, "$desktop_dir/RGB-MASTER-create-$date") ? 'Done successfully' : 'Cannot be done');
    scroll_to_bottom();

    remove_dir($temp_dir);

    ?>
  </p>

</body>

</html>