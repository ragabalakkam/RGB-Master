<?php

namespace App\Http\Controllers\API\Clients;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;

# models
use App\Models\Apps\AppClient;

# facades
use App\Helpers\Classes\RemoteManagement\RemoteAppManager;

class RemoteManagementController extends Controller
{
    public function check_status(AppClient $app_client)
    {
        $manager = new RemoteAppManager($app_client);
        $channel = $manager->broadcastOn();
        $count = $manager->getSubscribersCount();
        return response()->json(['channel' => $channel, 'online' => $count]);
    }

    public function execute_command(AppClient $app_client, Request $request)
    {
        $output = RemoteAppManager::sendCommand($app_client, $request->command);
        return response()->json($output);
    }
}
