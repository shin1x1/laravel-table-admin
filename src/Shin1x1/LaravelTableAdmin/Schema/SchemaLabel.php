<?php
namespace Shin1x1\LaravelTableAdmin\Schema;

/**
 * Class SchemaLabel
 * @package Shin1x1\LaravelTableAdmin\Schema
 */
class SchemaLabel extends AbstractSchema
{
    /**
     * @return bool
     */
    public function isLabel()
    {
        return true;
    }
}
