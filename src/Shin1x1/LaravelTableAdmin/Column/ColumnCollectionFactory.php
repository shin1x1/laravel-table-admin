<?php
namespace Shin1x1\LaravelTableAdmin\Column;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

/**
 * Class ColumnCollectionFactory
 * @package Shin1x1\LaravelTableAdmin\Column
 */
class ColumnCollectionFactory
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $table
     * @return ColumnCollection
     */
    public function factory($table)
    {
        $schemas = $this->getColumnSchemas($table);
        /** @var Collection $foreignKeyColumns */
        /** @var Collection $foreignTables */
        list($foreignKeyColumns, $foreignTables) = $this->getForeignKeys($table);

        $columns = ColumnCollection::make([]);
        foreach ($schemas as $column) {
            $column = $this->buildColumn($column, $foreignKeyColumns, $foreignTables);
            $columns->push($column);
        }

        return $columns;

    }

    /**
     * @param Column $column
     * @param Collection $foreignKeyColumns
     * @param Collection $foreignTables
     * @return ColumnInterface
     */
    protected function buildColumn(Column $column, Collection $foreignKeyColumns, Collection $foreignTables)
    {
        if ($column->getAutoincrement()) {
            return new ColumnLabel($column);

        } else if ($foreignKeyColumns->has($column->getName())) {
            $table = $foreignKeyColumns->get($column->getName());
            return new ColumnSelect($column, $foreignTables->get($table));

        } else {
            return new ColumnText($column);
        }
    }

    /**
     * @param string $table
     * @return array
     */
    protected function getColumnSchemas($table)
    {
        $columns = $this->connection->getDoctrineSchemaManager();
        return $columns->listTableColumns($table);
    }

    /**
     * @param string $table
     * @return array [Collection, Collection]
     */
    protected function getForeignKeys($table)
    {
        $foreignTables = Collection::make([]);
        $foreignKeyColumns = Collection::make([]);

        Collection::make($this->connection->getDoctrineSchemaManager()->listTableForeignKeys($table))
            ->each(function($key) use ($foreignTables, $foreignKeyColumns) {
                /** @var ForeignKeyConstraint $key */
                if (count($key->getLocalColumns()) != 1) {
                    return;
                }

                $column = $key->getLocalColumns()[0];

                $table = $key->getForeignTableName();
                $foreignKeyColumns->put($column, $table);

                if ($foreignTables->has($table)) {
                    return;
                }

                $foreignTables->put($table, $this->connection->table($table)->orderBy('id')->lists('name', 'id'));
            }
        );

        return [$foreignKeyColumns, $foreignTables];
    }
}