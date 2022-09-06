<?php

namespace App\Http\Controllers\API\Organizations;

# controllers
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Apps\InstallationRequestsController;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Apps\InstallationRequest;
use App\Http\Requests\Organizations\OrganizationRequest;

# models
use App\Models\Users\Customer;
use App\Models\Clients\Client;
use App\Models\Apps\AppClient;

class OrganizationsController extends Controller
{
    public function index()
    {
        $customer = Customer::findOrFail(auth()->id());
        $organizations = $customer->organizations()->with(['image'])->get();
        return response()->json($organizations);
    }
    
    public function store(OrganizationRequest $request)
    {
        $organization = Client::create($request->all());
        $organization->storeImg($request->image);
        Customer::findOrFail(auth()->id())->organizations()->attach($organization);
        return response()->json($organization);
    }
    
    public function show(Client $organization)
    {
        return response()->json($organization);
    }
    
    public function update(Request $request, Client $organization)
    {
        $organization->update($request->all());
        $organization->updateImg($request->image);
        return response()->json($organization);
    }
    
    public function destroy(Client $organization)
    {
        $organization->delete();
        return response()->json();
    }

    // 

    public function getClientApps(Client $organization)
    {
        $client_apps = $organization->client_apps()->protected()->get();
        return response()->json($client_apps);
    }

    public function getClientApp(Client $organization, AppClient $app)
    {
        if ($app->client_id !== $organization->id)
            return response()->json(null, 403);

        $app = AppClient::protected()->where('id', $app->id)->get();
        return response()->json($app);
    }
}
