<?php
namespace Shin1x1\LaravelTableAdmin\Column;

/**
 * Class ColumnSelect
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnSelect extends AbstractColumn
{
    /**
     * @return bool
     */
    public function isSelect()
    {
        return true;
    }
}
