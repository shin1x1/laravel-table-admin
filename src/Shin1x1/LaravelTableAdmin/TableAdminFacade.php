<?php
namespace Shin1x1\LaravelTableAdmin;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

/**
 * Class TableAdminFacade
 * @package Shin1x1\LaravelTableAdmin
 */
class TableAdminFacade extends Facade
{
    /**
     * @param array $tables
     */
    public static function route(array $tables)
    {
        Route::group(Config::get(TableAdmin::PACKAGE_NAME . '::routing'), function() use ($tables) {
            $parameters = [
                'table' => '(' . implode('|', $tables) . ')',
            ];

            Route::get('', function() use($tables) {
                return View::make(TableAdmin::PACKAGE_NAME .'::'. 'tables', compact('tables'));
            });

            $controller = '\Shin1x1\LaravelTableAdmin\Controller\TableAdminController';
            Route::get('{table}', $controller . '@index')->where($parameters);
            Route::get('{table}/create', $controller . '@create')->where($parameters);
            Route::post('{table}', $controller . '@store')->where($parameters);
            Route::get('{table}/{id}', $controller . '@edit')->where($parameters);
            Route::put('{table}/{id}', $controller . '@update')->where($parameters);
            Route::delete('{table}/{id}', $controller . '@destroy')->where($parameters);
        });
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'table-admin'; }
}
