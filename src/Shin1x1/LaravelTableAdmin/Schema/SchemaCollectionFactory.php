<?php
namespace Shin1x1\LaravelTableAdmin\Schema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class SchemaCollectionFactory
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
     * @return Collection
     */
    public function factory($table)
    {
        $columns = $this->getColumns($table);
        /** @var Collection $foreignKeyColumns */
        /** @var Collection $foreignTables */
        list($foreignKeyColumns, $foreignTables) = $this->getForeignKeys($table);

        $schemas = Collection::make([]);
        foreach ($columns as $column) {
            $schema = $this->buildSchema($column, $foreignKeyColumns, $foreignTables);
            $schemas->push($schema);
        }

        return $schemas;

    }

    /**
     * @param Column $column
     * @param Collection $foreignKeyColumns
     * @param Collection $foreignTables
     * @return SchemaInterface
     */
    protected function buildSchema(Column $column, Collection $foreignKeyColumns, Collection $foreignTables)
    {
        if ($column->getAutoincrement()) {
            return new SchemaLabel($column);

        } else if ($foreignKeyColumns->has($column->getName())) {
            $table = $foreignKeyColumns->get($column->getName());
            return new SchemaSelect($column, $foreignTables->get($table));

        } else {
            return new SchemaText($column);
        }
    }

    /**
     * @param string $table
     * @return array
     */
    protected function getColumns($table)
    {
        $schema = $this->connection->getDoctrineSchemaManager();
        return $schema->listTableColumns($table);
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