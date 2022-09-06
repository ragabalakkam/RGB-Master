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
    '@cache',
    '@license',
    '@m',
    'fonts',
    'webfonts',
    'storage',
    'plugins',
    'imgs',
    'sounds',
    '.htaccess',
    'favicon.ico',
    'index.php',
    'mix-manifest.json',
    'robots.txt',
    'web.config',
    'css/theme.css',
    'css/print_vars.css',
    'css/fontawesome.css',
    'js/app.js.LICENSE.txt',
    'js/dev.js.LICENSE.txt',
    'js/auth.js.LICENSE.txt',
    'js/master.js.LICENSE.txt',
    'js/print.min.js',
    'js/fontawesome.min.js',
  ],
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RGB Master | Update</title>

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
      }
      else recursive_copy($file_path, "$temp_dir/$file", $except, true);
      
      echo '
          </div>
        </div>
        ';
    }

  ?>

  <p class="m-3 p-2 mb-5 bg-success text-light rounded">
    <?php

      # export downloadeable file (including both update.zip and a one-time-run.bat)
      echo 'Zip : ' . (zip($temp_dir, "$desktop_dir/RGB-MASTER-update-$date") ? 'Done successfully' : 'Cannot be done');

      # remove temp dirs (and all contents)
      remove_dir($temp_dir);

      scroll_to_bottom();

    ?>
  </p>

</body>

</html>