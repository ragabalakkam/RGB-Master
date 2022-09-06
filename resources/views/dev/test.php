<?php

$require = [
  'addresses',
  'apps',
  'backups',
  'clients',
  'configurations',
  'cpanel',
  'database',
  'files',
  'helpers',
  'logs',
  'master',
  'notifications',
  'versions',
];

foreach ($require as $file) {
  require_once base_path("resources/views/dev/scripts/$file.php");
}

if (isset($_GET['func'])) {
  $func = $_GET['func'];
  $params = isset($_GET['params']) ? $_GET['params'] : [];
  dd($func(...$params));
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

?>

<html>

<head>
  <title><?= config('app.name') ?></title>

  <link rel="shortcut icon" href="<?= asset('favicon.ico') ?>" />

  <link rel="stylesheet" href="<?= asset('css/fontawesome.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/theme.css?t=') . time() ?>" id="theme-stylesheet">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/print_vars.css?t=') . time() ?>">
  <link rel="stylesheet" href="<?= asset('css/print.css') ?>">
</head>

<body>
  <p class="bg-secondary m-4 text-light fs-8 p-5">
    <a href="/redirect" target="_blank">
      RGB Master
    </a>
  </p>

  <div class="m-4 px-5 pt-3 border">
    <ul>
      <li class="mb-3">
        <a href="/dev@afaqrgb/clear" target="_blank">Clear</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=clear_master_logs" target="_blank">Clear master logs</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=get_stats" target="_blank">Get stats</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=collect_all_logs" target="_blank">Collect all logs</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=notify_masters" target="_blank">Notify masters</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=notify_devs_with_all_logs" target="_blank">Notify developers with logs</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=backup_all_apps" target="_blank">Backup all apps</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_old_clients" target="_blank">List old clients</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_new_clients" target="_blank">List new clients</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_duplicates" target="_blank">List duplicates</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_domains" target="_blank">List domains</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_subdomains" target="_blank">List subdomains</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_apps&params[]=1" target="_blank">List RGB Online apps</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_apps&params[]=2" target="_blank">List RGB Offline apps</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_outdated_clients" target="_blank">List outdated clients</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_jobs" target="_blank">List jobs</a>
      </li>
      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=list_failed_jobs" target="_blank">List failed jobs</a>
      </li>

      <li class="mb-3">
        <a href="/dev@afaqrgb/test?func=scan_files" target="_blank">Scan files</a>
      </li>

      <li class="mb-3">
        <p>Find outdated databases by tables count</p>
        <form class="d-flex flex-gap-3" action="/dev@afaqrgb/test">
          <input type="hidden" name="func" value="outdated_dbs_by_tables_count">
          <div>
            <label for="count">Count</label>
            <input class="ht-25" id="count" name="params[]">
          </div>
          <button type="submit">find</button>
        </form>
      </li>
      <li class="mb-3">
        <p>Find outdated databases by column</p>
        <form class="d-flex flex-gap-3" action="/dev@afaqrgb/test">
          <input type="hidden" name="func" value="outdated_dbs_by_column">
          <div>
            <label for="table">Table</label>
            <input class="ht-25" id="table" name="params[]">
          </div>
          <div>
            <label for="column">Column</label>
            <input class="ht-25" id="column" name="params[]">
          </div>
          <button type="submit">find</button>
        </form>
      </li>

      <li class="mb-3">
        <p>Update client files:</p>
        <form class="d-flex flex-gap-3" action="/dev@afaqrgb/test">
          <input type="hidden" name="func" value="update_client_files">
          <div>
            <label for="client-dir">Client dir name</label>
            <input class="ht-25" id="client-dir" name="params[]">
          </div>
          <div>
            <label for="zip-path">Zip path</label>
            <input class="ht-25" id="zip-path" name="params[]">
          </div>
          <button type="submit">update</button>
        </form>
      </li>
      <li class="mb-3">
        <p>Update all clients files:</p>
        <form class="d-flex flex-gap-3" action="/dev@afaqrgb/test">
          <input type="hidden" name="func" value="update_all_client_files">
          <div>
            <label for="zip-path">Zip path</label>
            <input class="ht-25" id="zip-path" name="params[]">
          </div>
          <button type="submit">update</button>
        </form>
      </li>
    </ul>
  </div>
</body>

</html>