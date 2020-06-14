<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'api'

], function ($router) {
    /**
     * All the get request routes also have the post request routes.
     * Which should give more choices to my teammates.
     * Also, it doesn't require me to do any extra work.
     */
    Route::post('allstocks', 'ApisController@allstocks');
    Route::post('cash', 'ApisController@cash');
    Route::post('sell', 'ApisController@sell');
    Route::post('buy', 'ApisController@buy');

    Route::get('allstocks', 'ApisController@allstocks');
    Route::get('cash', 'ApisController@cash');
});



