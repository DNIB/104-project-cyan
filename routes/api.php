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

Route::prefix('location')->group( function(){
    Route::get('/{location}', 'TripManageController@showLocation');
});

Route::prefix('trip')->group( function(){
    Route::get('/getLocation/{trip_id}', 'LocationManageController@showTripLocation');
});

Route::prefix('user')->group( function(){
    Route::get('/getLocation/{trip_id}', 'LocationManageController@showUserLocation');
});