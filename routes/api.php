<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* VERSION 1 */

Route::group(['prefix' => 'v1'], function ()
{
    /*************
    *** MASTER ***
    *************/

    Route::group(['middleware' => ['auth:api']], function ()
    {
        # configurations
        Route::post('configurations/set-general-information',       'API\ConfigurationsController@setGeneralInformation')->name('master.setGeneralInformation');
        Route::post('configurations',                               'API\ConfigurationsController@store')->name('configurations.store');
        Route::put('configurations/{configuration}',                'API\ConfigurationsController@update')->name('configurations.update');
        Route::delete('configurations/{configuration}',             'API\ConfigurationsController@delete')->name('configurations.delete');

        # email info
        // Route::get('email-info',                                    'EmailsController@show')->name('master.getEmailInfo');
        // Route::put('update-email-credentials',                      'EmailsController@update')->name('master.updateEmailCredentials');

        # sms info
        // Route::get('sms-info',                                      'SmsController@show')->name('master.getSmsInfo');
        // Route::put('update-sms-credentials',                        'SmsController@update')->name('master.updateSmsCredentials');

        # themes
        // Route::get('themes/factory-reset',                          'API\ThemesController@factory_reset')->name('themes.resetDefaults');
        // Route::apiResource('themes',                                'API\ThemesController');

        # fonts
        // Route::post('fonts',                                        'API\FontsController@store')->name('fonts.create');
        // Route::post('fonts/{font}',                                 'API\FontsController@update')->name('fonts.update');
        // Route::delete('fonts/{font}',                               'API\FontsController@destroy')->name('fonts.destroy');

        # locales
        Route::get('locales/import/{locale?}',                      'API\Lang\LocalesController@import')->name('locales.import');
        Route::get('locales/export/{locale?}',                      'API\Lang\LocalesController@export')->name('locales.export');
        Route::get('locales/factory-reset',                         'API\Lang\LocalesController@factory_reset')->name('locales.resetDefaults');
        Route::post('locales',                                      'API\Lang\LocalesController@store')->name('locales.store');
        Route::put('locales/{locale}',                              'API\Lang\LocalesController@update')->name('locales.update');
        Route::delete('locales/{locale}',                           'API\Lang\LocalesController@delete')->name('locales.delete');

        # translations
        Route::post('translations',                                 'API\Lang\TranslationsController@store')->name('translations.store');
        Route::put('translations/{translation}',                    'API\Lang\TranslationsController@update')->name('translations.update');
        Route::delete('translations/{translation}',                 'API\Lang\TranslationsController@delete')->name('translations.destroy');

        # locations
        Route::apiResource('countries',                             'API\Locations\CountriesController');
        Route::apiResource('states',                                'API\Locations\StatesController');
        Route::apiResource('cities',                                'API\Locations\CitiesController');
        Route::apiResource('districts',                             'API\Locations\DistrictsController');
        Route::get('locations/country/{country}/states',            'API\Locations\StatesController@index')->name('countries.states');
        Route::get('locations/state/{state}/cities',                'API\Locations\CitiesController@index')->name('states.cities');
        Route::get('locations/city/{city}/districts',               'API\Locations\DistrictsController@index')->name('cities.districts');
        Route::get('locations/factory-reset',                       'API\Locations\LocationsController@factory_reset')->name('locations.resetDefaults');

        Route::post('upload-large-file',                            'API\FilesController@store')->name('upload_large_file');
    });

    #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

    /** NAMESPACED (API) **/

    Route::group(['namespace' => 'API'], function ()
    {
        /********************
        *** AUTHENTICATED ***
        ********************/

        Route::group(['middleware' => ['auth:api']], function ()
        {
            # notifications
            Route::apiResource('notifications',                                 'NotificationsController');
            Route::get('user/permissions',                                      'UsersController@getPermissions');

            # sales systems
            Route::apiResource('sales-systems',                                 'SalesSystemsController');

            # clients
            Route::get('clients/backups',                                       'Database\DatabaseController@client_backups')->name('client_backups.index');
            Route::group(['namespace' => 'Clients'], function () {
                Route::post('clients',                                              'ClientsController@store')->name('clients.store');
                Route::post('clients/{client}',                                     'ClientsController@update')->name('clients.update');
                Route::delete('clients/{client}',                                   'ClientsController@destroy')->name('clients.destroy');
                Route::get('clients',                                               'ClientsController@index')->name('clients.index');
                Route::get('clients/{client}',                                      'ClientsController@show')->name('clients.show');
                Route::get('clients/{client}/remove-image',                         'ClientsController@remove_image')->name('clients.remove_image');
                
                Route::get('client-apps/{app_client}',                              'ClientAppsController@show')->name('client_apps.show');
                Route::put('client-apps/{app_client}/install',                      'ClientAppsController@install')->name('client_apps.install');
                Route::put('client-apps/{app_client}/uninstall',                    'ClientAppsController@uninstall')->name('client_apps.uninstall');
                Route::put('client-apps/{app_client}/license',                      'ClientAppsController@license')->name('client_apps.license');
                Route::put('client-apps/{app_client}/unlicense',                    'ClientAppsController@unlicense')->name('client_apps.unlicense');
                Route::put('client-apps/{app_client}/update-business-type',         'ClientAppsController@update_business_type')->name('client_apps.update_business_type');
                Route::put('client-apps/{app_client}/update-configurations',        'ClientAppsController@update_configurations')->name('client_apps.update_configurations');
                Route::put('client-apps/{app_client}/update-domain',                'ClientAppsController@update_domain')->name('client_apps.update_domain');
                Route::put('client-apps/{app_client}/update-database',              'ClientAppsController@update_database')->name('client_apps.update_database');
                Route::post('client-apps/{app_client}/import-database',             'ClientAppsController@import_database')->name('client_apps.import_database');
                Route::get('client-apps/{app_client}/export-database',              'ClientAppsController@export_database')->name('client_apps.export_database');
                Route::get('client-apps/{app_client}/clean-database',               'ClientAppsController@clean_database')->name('client_apps.clean_database');
                Route::get('client-apps/{app_client}/update-version',               'ClientAppsController@update_version')->name('client_apps.update_version');
                Route::get('client-apps/{app_client}/check-for-updates',            'ClientAppsController@check_for_updates')->name('client_apps.check_for_updates');

                Route::post('client-apps/{app_client}/check-status',                'RemoteManagementController@check_status')->name('client_apps.check_status');
                Route::post('client-apps/{app_client}/execute-command',             'RemoteManagementController@execute_command')->name('client_apps.execute_command');
            });

            # versions
            Route::group(['namespace' => 'Versions'], function () {
                Route::post('versions',                                             'VersionsController@store')->name('versions.store');
                Route::post('versions/files/{name}',                                'VersionsController@updateFile')->name('versions.updateFile');
                Route::post('versions/{version}',                                   'VersionsController@update')->name('versions.update');
                Route::delete('versions/{version}',                                 'VersionsController@destroy')->name('versions.destroy');
                Route::get('versions',                                              'VersionsController@index')->name('versions.index');
                Route::get('versions/{version}/update-all-apps',                    'VersionsController@updateAllApps')->name('versions.update_all_apps');
                Route::get('versions/{version}/download',                           'VersionsController@download')->name('versions.download');
                Route::get('versions/{version}',                                    'VersionsController@show')->name('versions.show');
            });

            # apps
            Route::group(['namespace' => 'Apps'], function () {
                Route::post('apps',                                                 'AppsController@store')->name('apps.store');
                Route::post('apps/{app}',                                           'AppsController@update')->name('apps.update');
                Route::delete('apps/{app}',                                         'AppsController@destroy')->name('apps.destroy');
                Route::get('apps/{app}/client-apps',                                'AppsController@client_apps')->name('apps.client_apps');
            });


            /****************
            *** EMPLOYEES ***
            ****************/

            Route::group(['middleware' => []], function ()
            {
                # admins only
                Route::group([], function () {
                    # set default locale & theme
                    Route::post('configurations/set-default-locale',            'ConfigurationsController@setDefaultLocale')->name('locales.setDefault');
                    // Route::put('themes/set-default/{theme}',                    'ThemesController@setDefault')->name('themes.setDefault');

                    # db backup & restoration
                    // Route::get('database/backup',                               'DatabaseController@backup')->name('database.backup');
                    // Route::get('database/backups',                              'DatabaseController@backups')->name('database.backups');
                    // Route::get('database/restore',                              'DatabaseController@restore')->name('database.restore');
                    // Route::delete('database/delete',                            'DatabaseController@delete')->name('database.delete');
                    // Route::put('database/rename',                               'DatabaseController@rename')->name('database.rename');

                    # roles & permissions
                    Route::apiResource('roles',                                 'Roles\RolesController');
                    Route::apiResource('permissions',                           'Roles\PermissionsController');

                    # employees
                    Route::put('employees/{employee}/change-password',          'Users\EmployeesController@changePassword')->name('employees.change_password');
                    Route::apiResource('employees',                             'Users\EmployeesController');

                    # customers
                    Route::apiResource('customers',                             'Users\CustomersController');

                    # organizations & client-apps
                    Route::post('organizations/{organization}/apps',                'Organizations\CustomerOrganizationsController@store')->name('organizations.apps.store');
                    Route::get('organizations/{organization}/apps',                 'Organizations\CustomerOrganizationsController@index')->name('organizations.apps.index');
                    Route::get('organizations/{organization}/apps/{client_app}',    'Organizations\CustomerOrganizationsController@show')->name('organizations.apps.show');
                    Route::put('organizations/{organization}/apps/{client_app}',    'Organizations\CustomerOrganizationsController@update')->name('organizations.apps.update');
                    Route::delete('organizations/{organization}/apps/{client_app}', 'Organizations\CustomerOrganizationsController@delete')->name('organizations.apps.delete');
                    Route::apiResource('organizations',                             'Organizations\OrganizationsController');
                });
            });
        });

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        /*****************************************************
        *** PREFIXED BY (auth) ** DO NOT NEED AUTHENTICITY ***
        *****************************************************/

        Route::group(['prefix' => 'auth'], function () 
        {
            Route::post('login',                                                'Auth\AuthController@login')->name('login');
            Route::post('register',                                             'Auth\AuthController@register');
            Route::post('logout',                                               'Auth\AuthController@logout');
            Route::post('refresh',                                              'Auth\AuthController@refresh');
            Route::get('user',                                                  'Auth\AuthController@user');

            Route::post('requset-password-reset',                               'Auth\ResetPasswordController@requsetPasswordReset');
            Route::post('reset-password',                                       'Auth\ResetPasswordController@resetPassword');
            Route::get('check-reset-password-token/{token}',                    'Auth\ResetPasswordController@checkResetPasswordToken');
            Route::put('change-password',                                       'Auth\ResetPasswordController@changePassword');

            # email verification
            Route::post('verify-email',                                         'Auth\VerifyEmailController@verifyEmail');
            Route::get('resend-verification-link',                              'Auth\VerifyEmailController@resendVerificationLink');

            # update profile
            Route::put('update',                                                'UsersController@update');
            Route::post('change-profile-picture',                               'UsersController@changeProfilePicture');
        });

        #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

        /********************************************
        *** CUSTOMERS ** DO NOT NEED AUTHENTICITY ***
        ********************************************/

        Route::group([], function ()
        {
            # check fields ( unique : username, email, .. ) ( exists : username, .. )
            Route::post('check-unique-username',                                'Auth\CheckController@checkUniqueUsername');
            Route::post('check-unique-email',                                   'Auth\CheckController@checkUniqueEmail');
            Route::post('check-unique-phone',                                   'Auth\CheckController@checkUniquePhone');
            Route::post('check-exists-username',                                'Auth\CheckController@checkExistsUsername');

            # phone verification
            Route::post('send-phone-verification-sms',                          'Auth\VerifyPhoneController@store');
            Route::post('verify-phone',                                         'Auth\VerifyPhoneController@verify');

            # locales
            Route::get('locales',                                               'Lang\LocalesController@index');
            Route::get('locales/{locale}',                                      'Lang\LocalesController@show');

            # translations
            Route::get('translations',                                          'Lang\TranslationsController@index');
            Route::get('translations/{translation}',                            'Lang\TranslationsController@show');

            # get all locations
            Route::get('locations',                                             'Locations\LocationsController@index');

            # configurations
            Route::get('configurations',                                        'ConfigurationsController@index');
            Route::get('configurations/{configuration}',                        'ConfigurationsController@show');

            # business types
            Route::get('business-types',                                        'Master\BusinessTypesController@index')->name('business_types.index');
            Route::post('business-types',                                       'Master\BusinessTypesController@store')->name('business_types.store');
            Route::post('business-types/import',                                'Master\BusinessTypesController@import')->name('business_types.import');
            Route::post('business-types/{type}',                                'Master\BusinessTypesController@update')->name('business_types.update');
            Route::get('business-types/{type}',                                 'Master\BusinessTypesController@show')->name('business_types.show');
            Route::delete('business-types/{type}',                              'Master\BusinessTypesController@destroy')->name('business_types.destroy');
            Route::get('business-types/{type}/export',                          'Master\BusinessTypesController@export')->name('business_types.export');

            # apps (public)
            Route::get('apps',                                                  'Apps\AppsController@index')->name('apps.index');
            Route::get('apps/{app}',                                            'Apps\AppsController@show')->name('apps.show');
            Route::get('apps/{app}/versions',                                   'Apps\AppsController@get_versions')->name('apps.get_versions');
            Route::get('apps/{app}/latest-version',                             'Apps\AppsController@get_latest_version')->name('apps.get_latest_version');
            
            # client area
            Route::group(['middleware' => ['validate_client_request'], 'namespace' => 'ClientArea', 'prefix' => 'clients/{client}/apps/{app}'], function () {
                Route::put('installation-status',                               'ClientAppsController@update_insallation_status')->name('clientarea.client_apps.update_insallation_status');
                Route::get('latest-version',                                    'VersionsController@get_latest')->name('clientarea.versions.get_latest');
                Route::get('versions/{version}/download',                       'VersionsController@download')->name('clientarea.versions.download');

                # business-types
                Route::get('business-types',                                    'BusinessTypesController@index')->name('clientarea.business_types.index');
                Route::get('business-types/{type}',                             'BusinessTypesController@show')->name('clientarea.business_types.show');
                Route::put('business-type',                                     'BusinessTypesController@update')->name('clientarea.business_types.update');
            });

            # installation-requests
            Route::post('installation-requests',                                'Apps\InstallationRequestsController@store')->name('installation_requests.store');

            # contact-us
            Route::post('contact',                                              'Contacts\ContactsController@store')->name('contact_us');

            # bugs
            Route::post('report-bug',                                           'BugsController@store')->name('bugs.report');
        });
    });
});
