<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('/trip')->group( function(){
    Route::get('/index', 'TripManageController@index');
    Route::post('/location', 'TripManageController@createLocation');
    Route::put('/location', 'TripManageController@updateLocation');
    Route::delete('/location', 'TripManageController@deleteLocation');

    // Invalid Request
    Route::get('/', 'HomeController@invalidRequest');

    Route::post('/', 'TripManageController@createTrip');
    Route::put('/', 'TripManageController@updateTrip');
    Route::delete('/', 'TripManageController@deleteTrip');

    Route::put('/locationOrder', 'TripManageController@reorderLocation');

    Route::prefix('/viewPlayer')->group( function(){
        Route::get('/{trip_id}', 'TripPlayerController@index');

        // Invalid Request
        Route::get('/', 'HomeController@invalidRequest');

        Route::post('/', 'TripPlayerController@createPlayer');
        Route::put('/', 'TripPlayerController@updatePlayer');
        Route::delete('/', 'TripPlayerController@deletePlayer');
    });
});

Route::prefix('/location')->group( function(){
    Route::get('/{action}', 'LocationManageController@request');
    Route::post('/create', 'LocationManageController@createLocation');
    Route::post('/update', 'LocationManageController@updateLocation');
    Route::delete('/{target_id}', 'LocationManageController@deleteLocation');
});

Route::prefix('/user')->group( function(){
    // Invalid Request
    Route::get('/', 'HomeController@invalidRequest');

    Route::put('/', 'SuperUserController@update');
    Route::delete('/', 'SuperUserController@delete');
});
