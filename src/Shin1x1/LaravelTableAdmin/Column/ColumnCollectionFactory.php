<?php
namespace Shin1x1\LaravelTableAdmin\Column;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
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
     * @param string $tableName
     * @return ColumnCollection
     */
    public function factory($tableName)
    {
        $table = $this->connection->getDoctrineSchemaManager()->listTableDetails($tableName);
        $schemas = $table->getColumns();
        /** @var Collection $foreignKeyColumns */
        /** @var Collection $foreignTables */

        list($foreignKeyColumns, $foreignTables) = $this->getForeignKeys($table);

        $indexes = Collection::make($table->getIndexes());

        $columns = ColumnCollection::make([]);
        foreach ($schemas as $column) {
            $column = $this->buildColumn($column, $foreignKeyColumns, $foreignTables, $indexes);
            $columns->push($column);
        }

        return $columns;

    }

    /**
     * @param Column $column
     * @param Collection $foreignKeyColumns
     * @param Collection $foreignTables
     * @param Collection $indexes
     * @return ColumnInterface
     */
    protected function buildColumn(Column $column, Collection $foreignKeyColumns, Collection $foreignTables, Collection $indexes)
    {
        $uniqued = $indexes->filter(function($index) use ($column) {
            /** @type Index $index */
            return $index->getColumns()[0] == $column->getName() && $index->isUnique();
        })->count() > 0;

        if ($column->getAutoincrement()) {
            return new ColumnAutoincrement($column, null, null, $uniqued);

        } else if ($foreignKeyColumns->has($column->getName())) {
            $table = $foreignKeyColumns->get($column->getName());
            return new ColumnSelect($column, $table, $foreignTables->get($table), $uniqued);

        } else if ($column->getType()->getName() == Type::INTEGER) {
            return new ColumnNumericText($column, null, null, $uniqued);
        } else {
            return new ColumnText($column, null, null, $uniqued);
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
     * @param Table $table
     * @return array [Collection, Collection]
     */
    protected function getForeignKeys(Table $table)
    {
        $foreignTables = Collection::make([]);
        $foreignKeyColumns = Collection::make([]);

        Collection::make($table->getForeignKeys())
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