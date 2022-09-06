<?php

namespace App\Http\Controllers\API\Apps;

# controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Clients\ClientAppsController;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Apps\InstallationRequest;

# models
use App\Models\Clients\Client;

# job
use App\Jobs\Roles\NotifyMastersJob;

# notifications
use App\Notifications\Master\ClientApps\InstalledAppNotification;

class InstallationRequestsController extends Controller
{
    public function store(InstallationRequest $request)
    {
        $client = Client::create($request->all());
        $client->storeImg($request->image);

        # subscribe the client to selected apps
        foreach ($request->apps as $app) {
            if (isset($app['on']) && $app['on']) {
                $client_app = $client->attach_app($app['app_id'], $app);
                $client_app = $client_app->app_model() ?? $client_app;

                # validate
                if (method_exists($client_app, 'validate')) $client_app->validate();
            }
        }
        
        $client->client_apps;
        if ($client->creator) $client->creator->image;
        return response()->json($client);
    }
}
