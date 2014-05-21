laravel-table-admin
===================

[![Build Status](https://travis-ci.org/shin1x1/laravel-table-admin.svg?branch=master)](https://travis-ci.org/shin1x1/laravel-table-admin)

Laravel-Table-Admin Simple CRUD package for Laravel 4

[demo](http://laravel-table-admin.herokuapp.com/crud/classes)

## Installation

First, add dependency in composer.json

```
$ compoer require "shin1x1/laravel-table-admin" "dev-master"
```

or

```
{
    "require": {
        "shin1x1/laravel-table-admin": "dev-master"
    }
}
```

Execute composer install or update

```
$ composer install or update
```

Next, add ServiceProvider in `app/config/app.php`

```
'Shin1x1\LaravelTableAdmin\TAbleAdminServiceProvider`
```

Finally, you write routing for CRUD in `app/routes.php`

```
        Route::group(Config::get(TableAdmin::PACKAGE_NAME . '::routing'), function() {
            $tables = [ // specify table names
                'classes',
                'nationalities',
                'riders',
            ];
            $parameters = [
                'table' => '(' . implode('|', $tables) . ')',
            ];

            $controller = '\Shin1x1\LaravelTableAdmin\Controller\TableAdminController';
            Route::get('{table}', $controller . '@index')->where($parameters);
            Route::get('{table}/create', $controller . '@create')->where($parameters);
            Route::post('{table}', $controller . '@store')->where($parameters);
            Route::get('{table}/{id}', $controller . '@edit')->where($parameters);
            Route::put('{table}/{id}', $controller . '@update')->where($parameters);
            Route::delete('{table}/{id}', $controller . '@destroy')->where($parameters);
        });
    });



```

Done!

If you will open `http://localhost/crud/{TABLE}` in browser, you can access CRUD.
