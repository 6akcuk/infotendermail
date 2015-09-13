<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::any('/', 'IndexController@index');

Route::controllers([
        'auth' => 'Auth\AuthController',
        'password' => 'Auth\PasswordController'
]);

Route::group(['prefix' => 'admin'], function() {
    Route::group(['namespace' => 'Admin', 'middleware' => 'auth'], function() {
        Route::resource('/', 'DashboardController');
        Route::resource('users', 'UsersController');
        Route::resource('roles', 'RolesController');

        Route::get('contracts/setup', ['as' => 'admin.contracts.setup', 'uses' => 'ContractsController@setup']);
        Route::post('contracts/setup', ['as' => 'admin.contracts.setup', 'uses' => 'ContractsController@save']);
        Route::get('contracts/match', ['as' => 'admin.contracts.match', 'uses' => 'ContractsController@match']);
        Route::get('contracts/view', ['as' => 'admin.contracts.view', 'uses' => 'ContractsController@view']);

        Route::resource('contracts', 'ContractsController');

        Route::group(['namespace' => 'Geography'], function() {
            Route::resource('countries', 'CountriesController');
            Route::resource('cities', 'CitiesController');
        });
    });
});
