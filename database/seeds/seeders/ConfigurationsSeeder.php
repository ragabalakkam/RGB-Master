<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

# models
use App\Models\Configuration;
use App\Models\Configurations\ConfigurationGroup;
use App\Models\Configurations\Group;

class ConfigurationsSeeder extends Seeder
{
    public static function run()
    {
        if (Configuration::all()->count()) return;

        $live = config('app.live');
        
        $groups = [

            'app' => [
                'name'      => ['array',    localize(['RGB Master', 'RGB ماستر'])],
                'logo'      => ['string',   'APP/favicon.png'],
                'url'       => ['string',   config('app.url')],
                'live'      => ['boolean',  $live],
                'cache_dir' => ['string',   '@cache/'],
            ],

            'paths' => [
                'clients_root_dir'  => ['string', $live ? '/home/rgbksaco/RGB/clients' : 'C:/xampp/htdocs/rgbksa/clients'],
                'versions_root_dir' => ['string', $live ? '/home/rgbksaco/RGB/versions' : 'C:/xampp/htdocs/rgbksa/versions'],
                'ftp_dir'           => ['string', $live ? '/home/rgbksaco/RGB/FTP' : 'C:/xampp/htdocs/rgbksa/FTP'],
            ],

            'apps' => [
                'install_apps_immediately'  => ['boolean', $live],
                'update_apps_immediately'   => ['boolean', $live],
            ],
            
        ];

        $groups_from_keys = [

            // EX: 'general_information' => ['app_name', 'logo'],

        ];

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        foreach ($groups['paths'] as $path) {
            create_dir_if_not_exist($path[1]);
        }

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        foreach ($groups as $name => $config) {
            $group = Group::create(['key' => $name]);
            foreach ($config as $key => $value) {
                $configuraiton = Configuration::create([
                    'key'       => $key,
                    'datatype'  => $value[0],
                    'value'     => $value[1],
                ]);
                ConfigurationGroup::create([
                    'configuration_group_id'    => $group->id,
                    'configuration_id'          => $configuraiton->id,
                ]);
            }
        }

        foreach ($groups_from_keys as $key => $keys) {
            Group::createFromKeys([
                'key'   => $key,
                'keys'  => $keys,
            ]);
        }
    }
}