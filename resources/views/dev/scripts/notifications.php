<?php

# models
use App\Models\User;

# jobs
use App\Jobs\Roles\NotifyMastersJob;

# notifications
use App\Notifications\Master\ClientApps\InstalledAppNotification;
use App\Notifications\Master\CollectedLogsNotification;

# facades
use Illuminate\Support\Facades\DB;

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

function notify_masters($app = null)
{
  if (!$app) $app = get_latest_client()->client_apps[0];
  NotifyMastersJob::dispatch(InstalledAppNotification::class, $app);
}

function notify_devs_with_all_logs()
{
  $emails = [
    'minaalfy8@gmail.com',
  ];

  collect_all_logs();

  foreach (glob('/home/rgbksaco/RGB/logs/*.log', GLOB_BRACE) as $file) {
    $logs[substr(basename($file), 0, -4)] = $file;
  }

  if (isset($logs)) {

    $users = User::whereIn('email', $emails)->get();
    $count = $users->count();

    foreach ($users as $i => $user) {
      $user->notify(new CollectedLogsNotification($logs, $i + 1 == $count));
    }

    return 'Notification sent';
  }

  return 'No logs found';
}

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# jobs & failed_jobs

function list_jobs_or_failed($type, $name)
{
  return ($name
    ? DB::table($type)->whereRaw("JSON_EXTRACT(`payload`,'$.displayName') = '" . str_replace("\\", "\\\\", backward_slashes($name)) . "'")
    : DB::table($type)
  )->get()->toArray();
}

function list_jobs($type = null)
{
  return list_jobs_or_failed('jobs', $type);
}

function list_failed_jobs($type = null)
{
  return list_jobs_or_failed('failed_jobs', $type);
}
