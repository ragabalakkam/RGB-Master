<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

# models
use App\Models\Lang\Translation;
use App\Models\Locations\Country;

function localize($arr)
{
    return ['ar' => $arr[1], 'en' => $arr[0]];
}

function random_id($model)
{
    return $model->random(1)->first()->id;
}

function success_msg($text)
{
    echo "Seeded $text successfully\n";
}

class DatabaseSeeder extends Seeder
{
    public static function run()
    {
        ConfigurationsSeeder::run();
        success_msg('configurations');

        if (!Translation::all()->count()) app('App\Http\Controllers\API\Lang\LocalesController')->factory_reset();
        success_msg('locales');

        if (!Country::all()->count()) app('App\Http\Controllers\API\Locations\LocationsController')->factory_reset();
        success_msg('locations');

        UsersSeeder::run();
        success_msg('users');

        SalesSystemsSeeder::run();
        success_msg('sales_systems');

        PermissionsSeeder::run();
        success_msg('roles & permissions');

        AppsSeeder::run();
        success_msg('apps');
    }
}
