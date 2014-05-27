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

Next, add ServiceProvider and Facade in `app/config/app.php`

```
    'providers' => [
        // ....
        'Shin1x1\LaravelTableAdmin\TAbleAdminServiceProvider`
    ],
```

```
    'aliases' => [
        // ....
        'TableAdmin' => 'Shin1x1\LaravelTableAdmin\TableAdminFacade',
    ],
```

Finally, you specify table name that to be enable CRUD `app/routes.php`

```
TableAdmin::route([
    'classes',
    'nationalities',
    'riders',
]);
```

Done!

If you will open `http://localhost/crud/{TABLE}` in browser, you can access CRUD.
