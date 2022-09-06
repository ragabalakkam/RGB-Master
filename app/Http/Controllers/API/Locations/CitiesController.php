<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locations\CityRequest;

# models
use App\Models\Locations\City;
use App\Models\Locations\State;

class CitiesController extends Controller
{
    public function index(State $state = null)
    {
        return response()->json($state ? $state->cities : City::all());
    }

    public function store(CityRequest $request, $updateFile = true)
    {
        $city = City::create($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($city);
    }

    public function show(City $city)
    {
        return response()->json($city);
    }

    public function update(CityRequest $request, City $city, $updateFile = true)
    {
        $city->update($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($city);
    }

    public function destroy(City $city, $updateFile = true)
    {
        $city->delete();
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json();
    }

    //

    private function updateCachedLocationsFile()
    {
        app('App\Http\Controllers\API\Locations\LocationsController')->index();
    }
}
