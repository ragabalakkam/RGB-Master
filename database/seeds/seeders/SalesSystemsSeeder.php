<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

# models
use App\Models\SalesSystem;

class SalesSystemsSeeder extends Seeder
{
    public static function run()
    {
        if (SalesSystem::all()->count()) return;


        $sales_systems = [
            ['takeaway',    ['takeaway',    'سفري'],   'coffee-togo'],
            ['dineIn',      ['dine-in',     'محلي'],   'utensils-alt'],
            ['delivery',    ['delivery',   'توصيل'],   'biking-mountain'],
        ];

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        foreach ($sales_systems as $key => $system) {
            SalesSystem::create([
                'key'   => $system[0],
                'name'  => localize($system[1]),
                'icon'  => $system[2] ?? null,
            ]);
        }
    }
}
