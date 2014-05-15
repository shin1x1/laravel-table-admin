<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Shin1x1\LaravelTableAdmin\TableAdmin;

Route::get('/', function () {
    return View::make('hello');
});

Route::group(Config::get(TableAdmin::PACKAGE_NAME . '::routing'), function() {
    $tables = [
        'classes',
        'nationalities',
        'riders',
    ];
    $parameters = [
        'table' => '(' . implode('|', $tables) . ')',
        'id' => '[0-9]+',
    ];

    $controller = '\Shin1x1\LaravelTableAdmin\Controller\TableAdminController';
    Route::get('{table}', $controller. '@index')->where($parameters);
    Route::get('{table}/create', $controller. '@create')->where($parameters);
    Route::post('{table}', $controller. '@store')->where($parameters);
    Route::get('{table}/{id}', $controller. '@edit')->where($parameters);
    Route::put('{table}/{id}', $controller. '@update')->where($parameters);
    Route::delete('{table}/{id}', $controller. '@destroy')->where($parameters);
});

