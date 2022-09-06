<?php

namespace App\Http\Controllers\API\Versions;

# controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Clients\ClientAppsController;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Versions\VersionRequest;
use App\Http\Requests\Versions\UpdateFileRequest;

# models
use App\Models\Versions\Version;
use App\Models\Apps\AppClient;
use App\Models\Clients\Client;

class VersionsController extends Controller
{
    public function index()
    {
        return response()->json(Version::with('user')->get());
    }

    public function store(VersionRequest $request)
    {
        # user id
        $request->merge(['user_id' => auth()->id()]);

        # copy file
        $filename = getConfig('versions_root_dir') . '/' . date('d-m-Y') . '-' . time() . '.' . pathinfo($request->file, PATHINFO_EXTENSION);
        create_dir_if_not_exist(dirname($filename));
        $request->merge(['path' => rename(public_path($request->file), $filename) ? $filename : null]);

        $version = Version::create($request->all());
        return response()->json($version);
    }

    public function show(Version $version)
    {
        $version->user;
        return response()->json($version);
    }

    public function update(Version $version, VersionRequest $request)
    {
        $version->update($request->except(['path']));
        return response()->json($version);
    }

    public function destroy(Version $version)
    {
        $version->delete();
        return response()->json(null);
    }

    // 

    public function download(Version $version)
    {
        $downloads_dir = public_path('storage/downloads');
        create_dir_if_not_exist($downloads_dir);

        $filename = $version->number . '.zip';
        copy($version->path, $downloads_dir . '/' . $filename);

        return response()->json("/storage/downloads/$filename");
    }

    public function updateFile($name, UpdateFileRequest $request)
    {
        if (!in_array($name, ['vendor', 'node_modules']))
            return response()->json(['errors' => ['file' => ['invalid']]], 422);

        $versions_root_dir = getConfig('versions_root_dir');
        create_dir_if_not_exist($versions_root_dir);

        if (!copy($request->file->getRealPath(), $versions_root_dir . "/" . $request->name . ".zip"))
            return response()->json(['errors' => ['file' => ['invalid']]], 422);

        return response()->json();
    }

    public function updateAllApps(Version $version)
    {
        $client_apps = $version->app->client_apps()->whereNotNull('installed_at')->where('version_id', '!=', $version->id)->whereNull('active_process')->get();
        foreach ($client_apps as $app) {
            app(ClientAppsController::class)->update_version($app, new Request(['version_id' => $version->id]));
        }
        return response()->json($client_apps->pluck('id')->toArray());
    }

    // 

    public function client_download(Client $client, AppClient $app, Version $version)
    {
        if ($app->client_id != $client->id)
            return response()->json(['errors' => ['app_id' => 'invalid']], 422);
            
        if ($version->app_id != $app->app_id)
            return response()->json(['errors' => ['version_id' => 'invalid']], 422);

        $downloads_dir = public_path('storage/downloads');
        create_dir_if_not_exist($downloads_dir);

        $filename = $version->number . '.zip';
        $destpath = $downloads_dir . '/' . $filename;
        if (!file_exists($destpath)) copy($version->path, $destpath);

        return response()->json("/storage/downloads/$filename");
    }
}
