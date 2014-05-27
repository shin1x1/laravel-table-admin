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

    /**
     * @return string
     */
    public function getValidationRule()
    {
        $rule = parent::getValidationRule();

        return $rule . '|regex:/\A[0-9]+\z/|exists:' . $this->getForeignTable() . ',id';
    }
}
