<?php
namespace Shin1x1\LaravelTableAdmin\Test\Column;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use PDO;
use Shin1x1\LaravelTableAdmin\Column\ColumnCollectionFactory;

/**
 * Class AbstractColumnCollectionFactoryTest
 * @package Shin1x1\LaravelTableAdmin\Test\Column
 */
abstract class AbstractColumnCollectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ColumnCollectionFactory
     */
    protected $sut;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @return string
     */
    abstract protected function getConfig();

    /**
     * @return string
     */
    abstract protected function getConnectionClass();

    /**
     * setUp
     */
    public function setUp()
    {
        $configs = include(__DIR__ . '/../test_database.php');
        $config = array_get($configs, $this->getConfig());

        $dsn = sprintf('%s:dbname=%s', $config['driver'], $config['database']);

        $pdo = new PDO($dsn, $config['username'], $config['password']);

        $class = $this->getConnectionClass();
        $this->connection = new $class($pdo);

        $this->connection->getSchemaBuilder()->dropIfExists('riders');
        $this->connection->getSchemaBuilder()->dropIfExists('classes');

        $this->connection->getSchemaBuilder()->create('classes', function(Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name');
        });

        $this->connection->table('classes')->insert([
            ['id' => '1', 'name' => 'class1'],
            ['id' => '2', 'name' => 'class2'],
        ]);

        $this->connection->getSchemaBuilder()->create('riders', function(Blueprint $table) {
            $table->increments('id')->primary();
            $table->integer('class_id')->index();
            $table->foreign('class_id')->references('id')->on('classes')->onUpdate('cascade');
            $table->string('name');
        });

        $this->sut = new ColumnCollectionFactory($this->connection);
    }

    public function tearDown()
    {
        $this->connection->getSchemaBuilder()->dropIfExists('riders');
        $this->connection->getSchemaBuilder()->dropIfExists('classes');
    }

    /**
     * @test
     */
    public function tableNothing()
    {
        $columns = $this->sut->factory('nothing');

        $this->assertEquals(0, $columns->count());
    }

    /**
     * @test
     */
    public function tableHasIdAndNameColumn()
    {
        $columns = $this->sut->factory('classes');

        $this->assertEquals(2, $columns->count());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnNumericText', $columns->get(0));
        $this->assertEquals('id', $columns->get(0)->getName());
        $this->assertTrue($columns->get(0)->uniqued());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnText', $columns->get(1));
        $this->assertEquals('name', $columns->get(1)->getName());
        $this->assertFalse($columns->get(1)->uniqued());

        $expected = [
            'id' => 'required|regex:/\A[0-9]+\z/',
            'name' => 'required',
        ];
        $this->assertEquals($expected, $columns->getValidateRules()->toArray());
    }

    /**
     * @test
     */
    public function tableHasForiegnKeyColumn()
    {
        $columns = $this->sut->factory('riders');

        $this->assertEquals(3, $columns->count());

        $column = $columns->get(0);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnAutoincrement', $column);
        $this->assertEquals('id', $column->getName());
        $this->assertTrue($columns->get(0)->uniqued());

        $column = $columns->get(1);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnSelect', $column);
        $this->assertEquals('class_id', $column->getName());
        $expected = [
            '1' => 'class1',
            '2' => 'class2',
        ];
        $this->assertEquals($expected, $column->getSelectList());
        $this->assertFalse($columns->get(1)->uniqued());

        $column = $columns->get(2);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnText', $column);
        $this->assertEquals('name', $column->getName());
        $this->assertFalse($columns->get(2)->uniqued());

        $expected = [
            'class_id' => 'required|regex:/\A[0-9]+\z/|exists:classes,id',
            'name' => 'required',
        ];
        $this->assertEquals($expected, $columns->getValidateRules()->toArray());
    }
}