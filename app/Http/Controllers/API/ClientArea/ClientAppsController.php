<?php

namespace App\Http\Controllers\API\ClientArea;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;

# models
use App\Models\Clients\Client;
use App\Models\Apps\AppClient;
use App\Models\Versions\Version;

# jobs
use App\Jobs\Roles\NotifyMastersJob;
use App\Jobs\Roles\NotifyDevelopersJob;

# notifications
use App\Notifications\Master\ClientApps\InstalledAppNotification;
use App\Notifications\Master\ClientApps\AppInstallationFailedNotification;
use App\Notifications\Master\ClientApps\UninstalledAppNotification;
use App\Notifications\Master\ClientApps\AppUninstallationFailedNotification;
use App\Notifications\Master\ClientApps\UpdatedAppNotification;
use App\Notifications\Master\ClientApps\AppUpdateFailedNotification;

class ClientAppsController extends Controller
{
    public function update_insallation_status(Client $client, AppClient $app, Request $request)
    {
        if (in_array($request->status, ['update_succeeded', 'update_failed'])) {
            $old_version = Version::find($request->old_version_id) ?? $app->version;
            $new_version = Version::find($request->new_version_id ?? $app->app->latest_version_id);
        }

        switch ($request->status)
        {
            # installation

            case 'installation_started':
                $app->start_process('installation');
                break;

            case 'installation_succeeded':
                $app->end_process('installation', true);
                $app->setInstalled();
                NotifyMastersJob::dispatch(InstalledAppNotification::class, $app);
                break;

            case 'installation_failed':
                $app->end_process('installation', false);
                NotifyDevelopersJob::dispatch(AppInstallationFailedNotification::class, $app, $request->exception);
                break;


            # uninstallation

            case 'uninstallation_started':
                $app->start_process('uninstallation');
                break;

            case 'uninstallation_succeeded':
                $app->end_process('uninstallation', true);
                $app->setInstalled(false);
                NotifyMastersJob::dispatch(UninstalledAppNotification::class, $app);
                break;

            case 'uninstallation_failed':
                $app->end_process('uninstallation', false);
                NotifyDevelopersJob::dispatch(AppUninstallationFailedNotification::class, $app, $request->exception);
                break;

                
            # update

            case 'update_started':
                $app->start_process('update');
                break;

            case 'update_terminated':
                $app->end_process('update');
                break;

            case 'update_succeeded':
                $app->end_process('update', true);
                $app->setVersion($new_version);
                NotifyMastersJob::dispatch(UpdatedAppNotification::class, $app, $old_version, $new_version);
                break;

            case 'update_failed':
                $app->end_process('update', false);
                NotifyDevelopersJob::dispatch(AppUpdateFailedNotification::class, $app, $old_version, $new_version, $request->exception);
                break;


            # license

            case 'license_started':
                $app->start_process('license');
                break;

            case 'license_succeeded':
                $app->end_process('license', true);
                $app->setLicensed();
                break;

            case 'license_failed':
                $app->end_process('license', false);
                break;
        }

        return response()->json($app->only([
            'id',
            'name',
            'app_id',
            'client_id',
            'version_id',
            'active_process',
            'started_process_at',
            'installation_time',
            'uninstallation_time',
            'update_time'
        ]));
    }
}
