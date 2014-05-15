<?php
namespace Shin1x1\LaravelTableAdmin\Schema;

interface SchemaInterface
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
}