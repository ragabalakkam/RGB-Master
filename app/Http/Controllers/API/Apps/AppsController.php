<?php

namespace App\Http\Controllers\API\Apps;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Apps\AppRequest;

# models
use App\Models\Apps\App;
use App\Models\Apps\AppClient;
use App\Models\Apps\Configurations\AppConfigurationGroup;

class AppsController extends Controller
{
    public function index()
    {
        return response()->json(App::with('image')->with('configuration_groups')->get());
    }
    
    public function store(AppRequest $request)
    {
        $app = App::create($request->except(['image', 'configuration_groups']));
        $app->storeImg($request->image);

        if ($request->configuration_groups) {
            foreach ($request->configuration_groups as $group) {
                $created_group = $app->configuration_groups()->create(
                    array_diff_key($group, array_flip(['configurations']))
                );
                foreach ($group['configurations'] as $configuration) {
                    $created_config = $created_group->configurations()->create(array_merge($configuration, ['app_id' => $app->id]));
                    AppConfigurationGroup::create([
                        'app_configuration_id'          => $created_config->id,
                        'app_configuration_group_id'    => $created_group->id,
                    ]);
                }
            }
        }

        return response()->json($app);
    }
    
    public function show(App $app)
    {
        $app->image;
        $app->configuration_groups;
        return response()->json($app);
    }
    
    public function update(AppRequest $request, App $app)
    {
        $app->update($request->all());
        $app->updateImg($request->image);
        
        $app->configuration_groups()->delete();
        $app->configurations()->delete();

        if ($request->configuration_groups) {
            foreach ($request->configuration_groups as $group) {
                $created_group = $app->configuration_groups()->create(
                    array_diff_key($group, array_flip(['configurations']))
                );
                foreach ($group['configurations'] as $configuration) {
                    $created_config = $created_group->configurations()->create(array_merge($configuration, ['app_id' => $app->id]));
                    AppConfigurationGroup::create([
                        'app_configuration_id'          => $created_config->id,
                        'app_configuration_group_id'    => $created_group->id,
                    ]);
                }
            }
        }

        return response()->json($app);
    }
    
    public function destroy(App $app)
    {
        $app->delete();
        return response()->json();
    }

    //

    private static $show_in_response = [
        'id',
        'type',
        'number',
        'stable',
        'path',
        'description',
        'created_at'
    ];

    public function get_versions(App $app)
    {
        $versions = $app->versions;
        foreach ($versions as $key => $version) {
            $versions[$key] = $version->only(self::$show_in_response);
        }
        return response()->json($versions);
    }

    public function get_latest_version(App $app)
    {
        $version = $app->versions->sortByDesc('id')->first()->only(self::$show_in_response);
        return response()->json($version);
    }

    //

    public function client_apps(App $app)
    {
        return response()->json(AppClient::where('app_id', $app->id)->orderBy('created_at', 'asc')->get());
    }
}
