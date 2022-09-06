<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;

# models
use App\Models\SalesSystem;

class SalesSystemsController extends Controller
{
    public function index()
    {
        return response()->json(SalesSystem::all());
    }

    public function store(Request $request)
    {
        $system = SalesSystem::create($request->all());
        return response()->json($system);
    }

    public function show(SalesSystem $system)
    {
        return response()->json($system);
    }

    public function update(SalesSystem $system, Request $request)
    {
        $system->update($request->all());
        return response()->json($system);
    }

    public function destroy(SalesSystem $system)
    {
        $system->delete();
        return response()->json(null);
    }
}
