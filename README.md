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
        "shin1x1/laravel-table-admin": "0.1.*"
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

## Customization

### View template files

If you run the below command then view template files will be publish to `app/views/packages/shin1x1/laravel-table-admin/`.

```
$ php artisan view:publish shin1x1/laravel-table-admin
Views published for package: shin1x1/laravel-table-admin
```

Published view template files include 3 files.`base.blade.php` is base layout file.`form.blade.php` is create and edit form page.`index.blade.php` is index page.

```
$ ls app/views/packages/shin1x1/laravel-table-admin/
base.blade.php  form.blade.php  index.blade.php
```

## Example

Example using this package is below repo.

[https://github.com/shin1x1/laravel-table-admin-example](https://github.com/shin1x1/laravel-table-admin-example)

