<?php
namespace Shin1x1\LaravelTableAdmin;

use Illuminate\Database\Connection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Shin1x1\LaravelTableAdmin\Schema\SchemaInterface;

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
    protected $schemas;

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
     * @param Collection $schemas
     * @param string $table
     * @param array $configs
     */
    public function __construct(Connection $connection, Collection $schemas, $table, array $configs = [])
    {
        $this->connection = $connection;
        $this->schemas = $schemas;
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
     * @return Collection
     */
    public function getValidationRules()
    {
        $rules = Collection::make([]);

        $this->schemas->filter(function($schema) {
            /** @var SchemaInterface $schema */
            return !$schema->isLabel();
        })->filter(function($schema) {
            /** @var SchemaInterface $schema */
            return $schema->required();
        })->each(function($schema) use ($rules) {
            /** @var SchemaInterface $schema */
            $rules->put($schema->getName(), 'required');
        });

        return $rules;
    }

    /**
     * @param array $inputs
     * $param integer $id
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

        $this->schemas->filter(function($schema) {
            /** @var SchemaInterface $schema */
            return !$schema->isLabel();
        })->each(function($schema) use ($all, $values) {
            /** @var SchemaInterface $schema */
            $name = $schema->getName();
            $values->put($name, array_get($all, $schema->getName()));
        });

        return $values->toArray();
    }
}
