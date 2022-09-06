<?php

use Illuminate\Support\Facades\Route;
use App\Models\Shortlink;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# For devs

Route::group(['prefix' => 'dev@afaqrgb'], function()
{
    Route::get('/clear', function () {
        return view('dev.clear');
    });
    
    Route::get('/test', function () {
        return view('dev.test');
    });
});

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

$regex = '[\/\w\.-]*';

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# captures wrong api calls (starts with /api/v1/)

Route::any('/api/v1/{api_capture}', function () {
    return 'cannot match any of the api calls';
})->where('api_capture', $regex);

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# auth
Route::get('/auth/{vue_capture?}', function () { return view('auth'); })->where('vue_capture', $regex);

# master
Route::get('/master/{vue_capture?}', function () { return view('master'); })->where('vue_capture', $regex);

# client
Route::get('/client/{vue_capture?}', function () { return view('client'); })->where('vue_capture', $regex);

# dev
Route::get('/dev/{vue_capture?}', function () { return view('dev'); })->where('vue_capture', $regex);

#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=

# captures all other calls

Route::any('/{vue_capture?}', function ($vue_capture = null) {
    if ($shortlink = Shortlink::find($vue_capture)) {
        if (is_null($shortlink->expires_at) || $shortlink->expires_at >= now()) {
            $shortlink->setVisited();
            
            if ($shortlink->only_one_visit)
                $shortlink->delete(); // setExpired();

            return redirect($shortlink->link);
        }
    }
    return view('app');
})->where('vue_capture', $regex);