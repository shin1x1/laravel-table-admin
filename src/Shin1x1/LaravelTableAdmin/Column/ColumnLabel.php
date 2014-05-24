<?php
namespace Shin1x1\LaravelTableAdmin\Column;

/**
 * Class ColumnLabel
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnLabel extends AbstractColumn
{
    /**
     * @return bool
     */
    public function isLabel()
    {
        return true;
    }
}
