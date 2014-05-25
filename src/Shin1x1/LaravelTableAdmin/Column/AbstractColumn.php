<?php
namespace Shin1x1\LaravelTableAdmin\Column;

use Doctrine\DBAL\Schema\Column;

/**
 * Class AbstractColumn
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class AbstractColumn implements ColumnInterface
{
    /**
     * @var Column
     */
    protected $column;

    /**
     * @var string
     */
    protected $foreignTable;

    /**
     * @var array
     */
    protected $selectList = [];

    /**
     * @param Column $column
     * @param string $foreignTable
     * @param array $selectList
     */
    public function __construct(Column $column, $foreignTable = '', $selectList = [])
    {
        $this->column = $column;
        $this->foreignTable = $foreignTable;
        $this->selectList = $selectList;
    }

    /**
     * @return boolean
     */
    public function isLabel()
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isSelect()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getSelectList()
    {
        return $this->selectList;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->column->getName();
    }

    /**
     * @return string
     */
    public function getForeignTable()
    {
        return $this->foreignTable;
    }

    /**
     * @return boolean
     */
    public function required()
    {
        return $this->column->getNotnull();
    }

    /**
     * @return string
     */
    public function getValidationRule()
    {
        if ($this->required() && !$this->isLabel()) {
           return 'required';
        }

        return null;
    }
}
