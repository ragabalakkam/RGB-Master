<?php

namespace App\Http\Controllers\API\ClientArea;

use App\Http\Controllers\API\Clients\ClientAppsController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# models
use App\Models\Apps\AppClient;
use App\Models\Clients\Client;
use App\Models\Versions\Version;

class VersionsController extends Controller
{
    public function get_latest(Client $client, AppClient $app, Request $request)
    {
        $app->check_for_updates();
        $version = $app->app->versions()->protected()->orderByDesc('id')->first();
        return response()->json($version);
    }

    public function download(Client $client, AppClient $app, Version $version, Request $request)
    {
        # validate that requested version belongs to the requesting app
        if ($version->app_id != $app->app_id)
            return response()->json(['errors' => ['version_id' => 'invalid']], 422);

        $downloads_dir = public_path('storage/downloads');
        create_dir_if_not_exist($downloads_dir);

        $filename = $version->number . '.zip';
        $destpath = $downloads_dir . '/' . $filename;
        if (!file_exists($destpath)) copy($version->path, $destpath);

        return response()->json("/storage/downloads/$filename");
    }

    public function update(Client $client, AppClient $app, Request $request)
    {
        $version = Version::firstOrFail($request->version_id);

        # validate that requested version belongs to the requesting app
        if ($version->app_id != $app->app_id)
            return response()->json(['errors' => ['version_id' => 'invalid']], 422);

        $response = app(ClientAppsController::class)->update_version($app, new Request(['version_id' => $version->id]));
        if ($response->getStatusCode() != 200) return $response;

        return response()->json();
    }
}
