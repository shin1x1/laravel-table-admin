<?php
namespace Shin1x1\LaravelTableAdmin\Test\Column;

/**
 * Class ColumnCollectionFactoryPostgreSQLTest
 * @package Shin1x1\LaravelTableAdmin\Test\Column
 */
class ColumnCollectionFactoryPostgreSQLTest extends AbstractColumnCollectionFactoryTest
{
    protected function getConfig()
    {
        return 'pgsql';
    }

    protected function getConnectionClass()
    {
        return '\Illuminate\Database\PostgresConnection';
    }
}