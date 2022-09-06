const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    // CSS
    // .sass('resources/sass/app.scss',        'public/css')
    // .sass('resources/sass/print.scss',      'public/css')
                                            
    // JS
    .extract(['vue', 'jquery'])
    .js('resources/js/apps/App/app.js',          'public/js')
    .js('resources/js/apps/Auth/auth.js',        'public/js')
    .js('resources/js/apps/Master/master.js',    'public/js')
    .js('resources/js/apps/Client/client.js',    'public/js')
    .js('resources/js/apps/Dev/dev.js',          'public/js')

    // AUTO-RELOAD
    .browserSync('master.rgbksa.io')