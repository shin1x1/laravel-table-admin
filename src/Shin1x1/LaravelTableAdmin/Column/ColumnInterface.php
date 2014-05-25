<?php
namespace Shin1x1\LaravelTableAdmin\Column;

/**
 * Interface ColumnInterface
 * @package Shin1x1\LaravelTableAdmin\Column
 */
interface ColumnInterface
{
    /**
     * @return boolean
     */
    public function isLabel();

    /**
     * @return boolean
     */
    public function isSelect();

    /**
     * @return array
     */
    public function getSelectList();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return boolean
     */
    public function required();

    /**
     * @return boolean
     */
    public function uniqued();

    /**
     * @return string
     */
    public function getValidationRule();
}