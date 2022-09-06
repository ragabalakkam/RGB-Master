<?php

namespace App\Http\Controllers\API\ClientArea;

use App\Http\Controllers\Controller;
use App\Models\Apps\AppClient;
use App\Models\Clients\Client;
use App\Models\Master\BusinessType;
use Illuminate\Http\Request;

class BusinessTypesController extends Controller
{
    public function index(Client $client, AppClient $app)
    {
        $business_types = BusinessType::all()->toArray();
        return response()->json($business_types);
    }

    public function show(Client $client, AppClient $app, BusinessType $type)
    {
        return response()->json($type->toArray());
    }
    
    public function update(Client $client, AppClient $app, Request $request)
    {
        $type = BusinessType::firstOrFail($request->business_type_id);

        # validate that requested business_type belongs to the requesting app
        if ($type->app_id != $app->app_id)
            return response()->json(['errors' => ['business_type_id' => 'invalid']], 422);

        $app->update(['business_type_id' => $type->id]);
    }
}
