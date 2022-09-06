<?php

namespace App\Http\Controllers\API\Locations;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

# requests
use Illuminate\Http\Request;
use App\Http\Requests\Locations\CountryRequest;
use App\Http\Requests\Locations\DistrictRequest;
use App\Http\Requests\Locations\CityRequest;
use App\Http\Requests\Locations\StateRequest;

# models
use App\Models\Locations\Country;
use App\Models\Locations\State;

# controllers
use App\Http\Controllers\API\Locations\CountriesController;
use App\Http\Controllers\API\Locations\StatesController;
use App\Models\Locations\City;
use App\Models\Locations\District;

class LocationsController extends Controller
{
    public function index()
    {
        $locations = [];
        $locations['countries'] = (new CountriesController)->index()->original;
        $locations['states'] = (new StatesController)->index()->original;
        // $locations['cities'] = (new CitiesController)->index()->original;
        // $locations['districts'] = (new DistrictsController)->index()->original;

        $cache_path = config('path.locations_cache');
        put_contents("$cache_path/locations.json", json_encode($locations));

        return response()->json($locations);
    }

    public function factory_reset()
    {
        # truncate location tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();
        DB::table('states')->truncate();
        DB::table('cities')->truncate();
        DB::table('districts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        # load countries
        $file_path = base_path(config('path.reset') . 'locations/Countries.json');
        $countries = json_decode(str_replace('\r\n', '', file_get_contents($file_path)));
        foreach ($countries as $country) {
            Country::create(['name' => localize($country)]);
        }

        # load states
        $file_path = base_path(config('path.reset') . 'locations/States.json');
        $states = json_decode(str_replace('\r\n', '', file_get_contents($file_path)));
        foreach ($states as $state) {
            State::create(['country_id' => $state[0], 'name' => localize($state[1])]);
        }

        # load Cities
        $file_path = base_path(config('path.reset') . 'locations/Cities.json');
        $rows = json_decode(str_replace('\r\n', '', file_get_contents($file_path)));
        foreach ($rows as $row) {
            City::create([
                'id'        => $row[0],
                'name'      => localize([$row[2], $row[1]]),
                'state_id'  => $row[3],
            ]);
        }

        # load Districts
        $file_path = base_path(config('path.reset') . 'locations/Districts.json');
        $rows = json_decode(str_replace('\r\n', '', file_get_contents($file_path)));
        foreach ($rows as $row) {
            District::create([
                'name'          => localize([$row[1], $row[0]]),
                'city_id'       => $row[2],
                'neCoordinates' => $row[3],
                'swCoordinates' => $row[4],
            ]);
        }

        $this->index();
        return response()->json('Factory reset successfully !');
    }
}
