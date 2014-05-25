<?php
namespace Shin1x1\LaravelTableAdmin\Column;

/**
 * Class ColumnAutoincrement
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnAutoincrement extends AbstractColumn
{
    /**
     * @return bool
     */
    public function isLabel()
    {
        return true;
    }
}
