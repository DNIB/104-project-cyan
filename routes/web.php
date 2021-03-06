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

Route::get(
    '/', function () {
        return view('welcome');
    }
);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->prefix('/trip')->group(
    function () {
        Route::get(
            '/', function () {
                abort(404);
            }
        );
        Route::post('/', 'TripManageController@createTrip');
        Route::put('/', 'TripManageController@updateTrip');
        Route::delete('/', 'TripManageController@deleteTrip');

        Route::prefix('/viewPlayer')->group(
            function () {
                Route::get(
                    '/', function () {
                        abort(404);
                    }
                );
                Route::post('/', 'TripPlayerController@createPlayer');
                Route::put('/', 'TripPlayerController@updatePlayer');
                Route::delete('/', 'TripPlayerController@deletePlayer');
        
                Route::get('/{trip_id}', 'TripPlayerController@index');
            }
        );

        Route::prefix('/location')->group(
            function () {
                Route::get(
                    '/', function () {
                        abort(404);
                    }
                );
                Route::post('/', 'TripManageController@createLocation');
                Route::put('/', 'TripManageController@updateLocation');
                Route::delete('/', 'TripManageController@deleteLocation');
            }
        );

        Route::get('/index', 'TripManageController@index');
        Route::get('/tripMap/{trip_id}', 'LocationManageController@tripMap');
        Route::put('/locationOrder', 'TripManageController@reorderLocation');
    }
);

Route::middleware('auth')->prefix('/location')->group(
    function () {
        Route::get('/create', 'LocationManageController@createUserLocation');
        Route::get('/read', 'LocationManageController@readUserLocation');
 
        Route::post('/', 'LocationManageController@createLocation');
        Route::put('/', 'LocationManageController@updateLocation');
        Route::delete('/', 'LocationManageController@deleteLocation');
    }
);

Route::middleware('auth')->middleware('auth.superuser')->prefix('/user')->group(
    function () {
        Route::get(
            '/', function () {
                abort(404);
            }
        );
        Route::get('/location', 'SuperUserController@showAllLocations');
        Route::get('/trip', 'SuperUserController@showAllTrips');
        Route::get('/player', 'SuperUserController@showAllPlayers');

        Route::put('/', 'SuperUserController@update');
        Route::delete('/', 'SuperUserController@delete');

        Route::put('/{type}', 'SuperUserController@updateData');
        Route::delete('/{type}', 'SuperUserController@deleteData');
    }
);
