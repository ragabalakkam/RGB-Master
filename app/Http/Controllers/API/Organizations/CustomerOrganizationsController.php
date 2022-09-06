<?php

namespace App\Http\Controllers\API\Organizations;

# controllers
use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Clients\AppClientRequest;

# models
use App\Models\Apps\App;
use App\Models\Clients\Client;
use App\Models\Apps\AppClient;

class CustomerOrganizationsController extends Controller
{
    public function index(Client $organization)
    {
        $client_apps = $organization->client_apps()->protected()->get();
        return response()->json($client_apps);
    }
    
    public function store(Client $organization, AppClientRequest $request)
    {
        $app = App::find($request->app_id);

        $client_app = $organization->attach_app($app, [
            'name'              => $request->name,
            'app_id'            => $request->app_id,
            'version_id'        => $app->latest_version_id,
            'business_type_id'  => $request->business_type_id ?? $app->business_types->first()->id ?? null,
            'db_driver'         => 'mysql',
            'db_host'           => 'localhost',
            'db_database'       => 'rgbksaco_rgb_' . $request->domain,
            'db_username'       => 'rgbksaco_rgb_' . $request->domain,
            'db_password'       => 'afaqrgb2000',
            'domain'            => $request->domain,
            'root_dir'          => $request->root_dir,
        ]);

        return response()->json($client_app);
    }
    
    public function show(Client $organization, AppClient $client_app)
    {
        if ($client_app->client_id !== $organization->id)
            return response()->json(null, 403);

        $client_app = AppClient::protected()->where('id', $client_app->id)->first();
        return response()->json($client_app);
    }
    
    public function update(Request $request, Client $organization)
    {
        $organization->update($request->all());
        $organization->updateImg($request->image);
        return response()->json($organization);
    }
    
    public function destroy(Client $organization, AppClient $client_app)
    {
        if ($client_app->client_id !== $organization->id)
            return response()->json(null, 403);

        $organization->delete();
        return response()->json();
    }
}
