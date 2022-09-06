<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

# controllers
use App\Http\Controllers\API\Roles\RolesController;

# requests
use App\Http\Requests\Roles\RoleRequest;

# models
use App\Models\Roles\PermissionsGroup;
use App\Models\Roles\Permission;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    public static function run()
    {
        # group >> permission >> actions
        
        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        # Seeding permissions

        $permissions = [
            'adminPanel' => [
                'access adminPanel'             => ['adminPanel.access'],
                'show master configurations'    => ['configurations.show'],
                'modify master configurations'  => ['configurations.update'],
            ],
            'businessTypes' => [
                'show businessTypes'            => ['business_types.index', 'business_types.show'],
                'create businessTypes'          => ['business_types.store'],
                'update businessTypes'          => ['business_types.update'],
                'delete businessTypes'          => ['business_types.destroy'],
            ],
            'settings' => [
                'change databaseSettings'       => ['settings.database'],
            ],
            'email' => [
                'getEmailInfo email'            => ['master.getEmailInfo'],
                'updateEmailCredentials email'  => ['master.updateEmailCredentials'],
            ],
            'messages' => [
                'getSmsInfo sms'                => ['master.getSmsInfo'],
                'updateSmsCredentials sms'      => ['master.updateSmsCredentials'],
            ],
            'locales' => [
                'create locales'                => ['locales.store'],
                'update locales'                => ['locales.update'],
                'delete locales'                => ['locales.destroy'],
                'import locales'                => ['locales.import'],
                'export locales'                => ['locales.export'],
                'setDefault locale'             => ['locales.setDefault'],
                'resetDefaults locales'         => ['locales.resetDefaults'],
            ],
            'translations' => [
                'create translations'           => ['translations.store'],
                'update translations'           => ['translations.update'],
                'delete translations'           => ['translations.destroy'],
            ],
            'themes' => [
                'create themes'                 => ['themes.store', 'fonts.store'],
                'update themes'                 => ['themes.update', 'fonts.update'],
                'delete themes'                 => ['themes.destroy', 'fonts.destroy'],
                'setDefault theme'              => ['themes.setDefault'],
                'resetDefaults themes'          => ['themes.resetDefaults'],
            ],
            'locations' => [
                'show locations'                => ['locations.countries.show', 'locations.states.show', 'locations.cities.show', 'locations.districts.show', 'locations.countries.index', 'locations.states.index', 'locations.cities.index', 'locations.districts.index'],
                'create locations'              => ['locations.countries.store', 'locations.states.store', 'locations.cities.store', 'locations.districts.store'],
                'update locations'              => ['locations.countries.update', 'locations.states.update', 'locations.cities.update', 'locations.districts.update'],
                'delete locations'              => ['locations.countries.destroy', 'locations.states.destroy', 'locations.cities.destroy', 'locations.districts.destroy'],
                'resetDefaults locations'       => ['locations.resetDefaults'],
            ],
            'departments' => [
                'show departments'              => ['departments.index', 'permissions.index'],
                'create departments'            => ['departments.store'],
                'edit departments'              => ['departments.update'],
                'delete departments'            => ['departments.destroy'],
            ],
            'employees' => [
                'show employees'                => ['employees.index', 'permissions.index'],
                'create employees'              => ['employees.store'],
                'edit employees'                => ['employees.update'],
                'delete employees'              => ['employees.destroy'],
                'change password'               => ['employees.change_password'],
                'modify roles'                  => ['employees.modify_roles'],
            ],
            'roles' => [
                'show roles'                    => ['roles.index', 'permissions.index'],
                'create roles'                  => ['roles.store'],
                'edit roles'                    => ['roles.update'],
                'delete roles'                  => ['roles.destroy'],
            ],
            'clients' => [
                'show clients'                  => ['clients.index', 'clients.show'],
                'create clients'                => ['clients.store'],
                'update clients'                => ['clients.update'],
                'delete clients'                => ['clients.destroy'],
            ],
            'apps' => [
                'create apps'                   => ['apps.store'],
                'update apps'                   => ['apps.update'],
                'delete apps'                   => ['apps.destroy'],
            ],
            'clientApps' => [
                'create apps'                   => ['clientApps.store'],
                'update apps'                   => ['clientApps.update'],
                'delete apps'                   => ['clientApps.destroy'],
                'install apps'                  => ['clientApps.install'],
                'uninstall apps'                => ['clientApps.uninstall'],
                'license apps'                  => ['clientApps.license'],
                'unlicense apps'                => ['clientApps.unlicense'],
                'modify configurations'         => ['clientApps.configurations.update'],
            ],
            'versions' => [
                'show versions'                 => ['versions.index', 'versions.show'],
                'create versions'               => ['versions.store'],
                'update versions'               => ['versions.update'],
                'delete versions'               => ['versions.destroy'],
            ],

            'technicalSupportSettings' => [
                'settings voiceCalls'           => ['settings.technical_support.voice_calls'],
                'settings videoCalls'           => ['settings.technical_support.video_calls'],
                'settings chat'                 => ['settings.technical_support.chat'],
                'settings selfService'          => ['settings.technical_support.self_service'],
                'settings email'                => ['settings.technical_support.email'],
                'settings sms'                  => ['settings.technical_support.sms'],
            ],
            'technicalSupport' => [
                'service voiceCalls'            => ['technical_support.voice_calls'],
                'service videoCalls'            => ['technical_support.video_calls'],
                'service chat'                  => ['technical_support.chat'],
                'service selfService'           => ['technical_support.self_service'],
                'service email'                 => ['technical_support.email'],
                'service sms'                   => ['technical_support.sms'],
            ],
            'marketingSettings' => [
                'settings facebook'             => ['settings.marketing.social_media.facebook'],
                'settings instagram'            => ['settings.marketing.social_media.instagram'],
                'settings whatsapp'             => ['settings.marketing.social_media.whatsapp'],
                'settings snapchat'             => ['settings.marketing.social_media.snapchat'],
                'settings bonusPointsProgram'   => ['settings.marketing.loyalty_programs.bonus_points_program'],
                'settings cashBackRewards'      => ['settings.marketing.loyalty_programs.cash_back_rewards'],
                'settings affiliate'            => ['settings.marketing.loyalty_programs.affiliate'],
                'settings discountCoupons'      => ['settings.marketing.loyalty_programs.discount_coupons'],
            ],
            'marketing' => [
                'marketing facebook'            => ['marketing.social_media.facebook'],
                'marketing instagram'           => ['marketing.social_media.instagram'],
                'marketing whatsapp'            => ['marketing.social_media.whatsapp'],
                'marketing snapchat'            => ['marketing.social_media.snapchat'],
                'service bonusPointsProgram'    => ['marketing.loyalty_programs.bonus_points_program'],
                'service cashBackRewards'       => ['marketing.loyalty_programs.cash_back_rewards'],
                'service affiliate'             => ['marketing.loyalty_programs.affiliate'],
                'service discountCoupons'       => ['marketing.loyalty_programs.discount_coupons'],
            ],
            'advertisementSettings' => [
                'settings sponsors'             => ['settings.marketing.advertisement.sponsors'],
                'settings adAreas'              => ['settings.marketing.advertisement.adAreas'],
            ],
            'advertisement' => [
                'service sponsors'              => ['marketing.advertisement.sponsors'],
                'service adAreas'               => ['marketing.advertisement.adAreas'],
            ],
        ];

        foreach ($permissions as $group_name => $permissions) {
            $group = PermissionsGroup::create(['name' => $group_name]);
            foreach ($permissions as $permission_name => $actions) {
                $permission = $group->permissions()->create(['name' => $permission_name]);
                foreach ($actions as $action) {
                    $permission->actions()->create(['name' => $action]);
                }
            }
        }

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        # Seeding Roles

        $all_permissions = Permission::all();

        $roles = [
            [
                'priority'      =>  101,
                'name'          =>  ['Master', 'ماستر'],
                'users'         =>  User::whereIn('username', ['eng.ragabal-akkam', 'minaalfy'])->get(),
                'permissions'   =>  $all_permissions->whereNotIn('name', [
                    'create versions',
                    'update versions',
                    'delete versions',
                ]),
            ],
            [
                'priority'      =>  90,
                'name'          =>  ['Developer', 'مطور'],
                'users'         =>  User::whereIn('username', ['minaalfy'])->get(),
                'permissions'   =>  $all_permissions->whereNotIn('name', [
                    'show clients',
                    'create clients',
                    'update clients',
                    'delete clients',
                    'show departments',
                    'create departments',
                    'edit departments',
                    'delete departments',
                    'show employees',
                    'create employees',
                    'edit employees',
                    'delete employees',
                    'modify roles',
                    'change password',
                    'show roles',
                    'create roles',
                    'edit roles',
                    'delete roles',
                    'service voiceCalls',
                    'service videoCalls',
                    'service chat',
                    'service selfService',
                    'service email',
                    'service sms',
                    'marketing facebook',
                    'marketing instagram',
                    'marketing whatsapp',
                    'marketing snapchat',
                    'service bonusPointsProgram',
                    'service cashBackRewards',
                    'service affiliate',
                    'service discountCoupons',
                    'service sponsors',
                    'service adAreas',
                ]),
            ],
            [
                'priority'      =>  20,
                'name'          =>  ['Customer Service Agent', 'موظف دعم فني'],
                'users'         =>  User::whereIn('username', [])->get(),
                'permissions'   =>  $all_permissions->whereIn('name', [
                    'access adminPanel',
                    'create locales',
                    'update locales',
                    'delete locales',
                    'show clients',
                    'create clientApps',
                    'update clientApps',
                    'delete clientApps',
                    'install clientApps',
                    'uninstall clientApps',
                    'license clientApps',
                    'unlicense clientApps',
                    'modify configurations',
                    'service voiceCalls',
                    'service videoCalls',
                    'service chat',
                    'service selfService',
                    'service email',
                    'service sms',
                    'marketing facebook',
                    'marketing instagram',
                    'marketing whatsapp',
                    'marketing snapchat',
                    'service bonusPointsProgram',
                    'service cashBackRewards',
                    'service affiliate',
                    'service discountCoupons',
                    'service sponsors',
                    'service adAreas',
                ]),
            ],
        ];

        foreach($roles as $role)
        {
            $created_role = app(RolesController::class)->store(new RoleRequest([
                'name'              => localize($role['name']),
                'permission_ids'    => $role['permissions']->pluck('id')->toArray(),
                'priority'          => $role['priority'] ?? null,
            ]))->original;
            
            foreach ($role['users'] as $user)
            {
                $user->employee()->giveRole($created_role);
            }
        }
    }
}
