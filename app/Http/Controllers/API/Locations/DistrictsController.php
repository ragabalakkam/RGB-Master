<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locations\DistrictRequest;
use App\Models\Locations\City;
# models
use App\Models\Locations\District;

class DistrictsController extends Controller
{

    public function index(City $city = null)
    {
        return response()->json($city ? $city->districts : District::all());
    }

    public function store(DistrictRequest $request, $updateFile = true)
    {
        $district = District::create($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($district);
    }

    public function show(District $district)
    {
        return response()->json($district);
    }

    public function update(DistrictRequest $request, District $district, $updateFile = true)
    {
        $district->update($request->all());
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json($district);
    }

    public function destroy(District $district, $updateFile = true)
    {
        $district->delete();
        if ($updateFile) $this->updateCachedLocationsFile();
        return response()->json();
    }

    //

    private function updateCachedLocationsFile()
    {
        app('App\Http\Controllers\API\Locations\LocationsController')->index();
    }
}
