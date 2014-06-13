<?php
namespace Shin1x1\LaravelTableAdmin;

use Illuminate\Database\Connection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Shin1x1\LaravelTableAdmin\Column\ColumnInterface;

/**
 * Class TableAdmin
 * @package Shin1x1\LaravelTableAdmin
 */
class TableAdmin
{
    const INDEX_LIMIT = 100;
    const PACKAGE_NAME = 'laravel-table-admin';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Collection
     */
    protected $columns;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $configs;

    /**
     * @param Connection $connection
     * @param Collection $columns
     * @param string $table
     * @param array $configs
     */
    public function __construct(Connection $connection, Collection $columns, $table, array $configs = [])
    {
        $this->connection = $connection;
        $this->columns = $columns;
        $this->table = $table;
        $this->configs = Collection::make($configs);
    }

    /**
     * @return Paginator
     */
    public function index()
    {
        return $this->getQueryBuilder()
                    ->orderBy('id', 'desc')
                    ->paginate($this->configs->get('items', static::INDEX_LIMIT));
    }

    /**
     * @param integer $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \stdclass
     */
    public function read($id)
    {
        return $this->getQueryBuilder()->where('id', $id)->first();
    }

    /**
     * @param array $inputs
     * $param integer $id
     * @param null $id
     */
    public function register(array $inputs, $id = null)
    {
        if ($id) {
            $this->getQueryBuilder()->where('id', $id)->update($inputs);
        } else {
            $this->getQueryBuilder()->insert($inputs);
        }
    }

    /**
     * $param integer $id
     */
    public function delete($id)
    {
        $this->getQueryBuilder()->delete($id);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQueryBuilder()
    {
        return $this->connection->table($this->table);
    }

    /**
     * @param array $all
     * @return array
     */
    public function getRegisterValues(array $all)
    {
        $values = Collection::make([]);

        $this->columns->filter(function(ColumnInterface $column) {
            return !$column->isLabel();
        })->each(function(ColumnInterface $column) use ($all, $values) {
            $name = $column->getName();
            $values->put($name, array_get($all, $column->getName()));
        });

        return $values->toArray();
    }
}
