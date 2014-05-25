<?php
namespace Shin1x1\LaravelTableAdmin\Column;

/**
 * Class ColumnNumericText
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnNumericText extends AbstractColumn
{
    /**
     * @return string
     */
    public function getValidationRule()
    {
        $rule = parent::getValidationRule();

        return $rule . '|regex:/\A[0-9]+\z/';
    }
}
