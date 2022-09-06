<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locations\StateRequest;

# models
use App\Models\Locations\Country;
use App\Models\Locations\State;

class StatesController extends Controller
{
    public function index(Country $country = null)
    {
        return response()->json($country ? $country->states : State::all());
    }

    public function store(StateRequest $request)
    {
        $state = State::create($request->all());
        $this->updateCachedLocationsFile();
        return response()->json($state);
    }

    public function show(State $state)
    {
        return response()->json($state);
    }

    public function update(StateRequest $request, State $state)
    {
        $state->update($request->all());
        $this->updateCachedLocationsFile();
        return response()->json($state);
    }

    public function destroy(State $state)
    {
        $state->delete();
        $this->updateCachedLocationsFile();
        return response()->json();
    }

    //

    private function updateCachedLocationsFile()
    {
        app('App\Http\Controllers\API\Locations\LocationsController')->index();
    }
}
