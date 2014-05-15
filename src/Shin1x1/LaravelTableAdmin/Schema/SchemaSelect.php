<?php
namespace Shin1x1\LaravelTableAdmin\Schema;

/**
 * Class SchemaSelect
 * @package Shin1x1\LaravelTableAdmin\Schema
 */
class SchemaSelect extends AbstractSchema
{
    /**
     * @return bool
     */
    public function isSelect()
    {
        return true;
    }
}
