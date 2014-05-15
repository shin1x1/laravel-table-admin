<?php
namespace Shin1x1\LaravelTableAdmin\Schema;

use Doctrine\DBAL\Schema\Column;

class AbstractSchema implements SchemaInterface
{
    /**
     * @var Column
     */
    protected $column;

    /**
     * @var array
     */
    protected $selectList = [];

    /**
     * @param Column $column
     * @param array $selectList
     */
    public function __construct(Column $column, $selectList = [])
    {
        $this->column = $column;
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
     * @return boolean
     */
    public function required()
    {
        return $this->column->getNotnull();
    }
}
