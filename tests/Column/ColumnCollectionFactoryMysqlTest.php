<?php
namespace Shin1x1\LaravelTableAdmin\Test\Column;

/**
 * Class ColumnCollectionFactoryMysqlTest
 * @package Shin1x1\LaravelTableAdmin\Test\Column
 */
class ColumnCollectionFactoryMysqlTest extends AbstractColumnCollectionFactoryTest
{
    protected function getConfig()
    {
        return 'mysql';
    }

    protected function getConnectionClass()
    {
        return '\Illuminate\Database\MysqlConnection';
    }
}