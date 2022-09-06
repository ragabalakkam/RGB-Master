<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locations\CountryRequest;

# models
use App\Models\Locations\Country;

class CountriesController extends Controller
{
    public function index()
    {
        return response()->json(Country::all());
    }

    public function store(CountryRequest $request, $updateFile = true)
    {
        $country = Country::create($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($country);
    }

    public function show(Country $country)
    {
        return response()->json($country);
    }

    public function update(CountryRequest $request, Country $country, $updateFile = true)
    {
        $country->update($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($country);
    }

    public function destroy(Country $country, $updateFile = true)
    {
        $country->delete();
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json();
    }

    //

    private function updateCachedLocationsFile()
    {
        app('App\Http\Controllers\API\Locations\LocationsController')->index();
    }
}
