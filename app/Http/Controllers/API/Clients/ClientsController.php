<?php

namespace App\Http\Controllers\API\Clients;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Clients\ClientRequest;

# models
use App\Models\Clients\Client;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::with(['image', 'client_apps'])->get();
        return response()->json($clients);
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create($request->all());
        $client->storeImg($request->image);

        # subscribe the client to selected apps
        foreach ($request->apps as $app) {
            if (isset($app['on']) && $app['on']) {
                $app_client = $client->attach_app($app['app_id'], $app);
                $app_client = $app_client->app_model() ?? $app_client;

                # validate
                if (method_exists($app_client, 'validate')) {
                    $app_client->validate();
                }

                # add installation process to queue
                if (getConfig('install_apps_immediately') && method_exists($app_client, 'install')) {
                    app(ClientAppsController::class)->install($app_client);
                }
            }
        }

        $client->client_apps;
        if ($client->creator) $client->creator->image;
        return response()->json($client);
    }

    public function show(Client $client)
    {
        $client->image;
        $client->client_apps;
        if ($client->creator) $client->creator->image;
        return response()->json($client);
    }

    public function update(Client $client, ClientRequest $request)
    {
        $client->update($request->all());
        $client->updateImg($request->image);

        foreach ($client->client_apps as $client_app) {
            if ($client_app->installed_at && $app = $client_app->app_model()) {
                if (method_exists($app, 'updateClientInfo')) {
                    $app->updateClientInfo();
                }
            }
        }

        return $this->show($client);
    }

    public function destroy(Client $client)
    {
        # unsbscribe the client from all apps
        foreach ($client->client_apps as $client_app) {
            if ($client_app->installed_at && $app = $client_app->app_model()) {
                app(ClientAppsController::class)->uninstall($app);
            }
        }

        $client->delete();
        return response()->json(null);
    }

    // 

    public function remove_image(Client $client)
    {
        if ($image = $client->image)
        {
            $image->delete();

            foreach ($client->client_apps as $client_app) {
                if ($app = $client_app->app_model()) {
                    if (method_exists($app, 'updateLogo'))
                        $app->updateLogo();
                }
            }
        }
        return response()->json($client);
    }
}
