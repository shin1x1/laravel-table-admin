<?php
namespace Shin1x1\LaravelTableAdmin\Test\Column;

use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Schema\Blueprint;
use PDO;
use Shin1x1\LaravelTableAdmin\Column\ColumnCollectionFactory;

/**
 * Class ColumnCollectionFactoryTest
 * @package Shin1x1\LaravelTableAdmin\Test\Column
 */
class ColumnCollectionFactoryTest extends \PHPUnit_Framework_TestCase
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
     * setUp
     */
    public function setUp()
    {
        $pdo = new PDO('pgsql:dbname=app_test', 'vagrant', 'app_test');
        $this->connection = new PostgresConnection($pdo);

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
            $table->increments('id');
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
    public function tableNothing(){
        $columns = $this->sut->factory('nothing');

        $this->assertEquals(0, $columns->count());
    }

    /**
     * @test
     */
    public function tableHasIdAndNameColumn(){
        $columns = $this->sut->factory('classes');

        $this->assertEquals(2, $columns->count());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnText', $columns->get(0));
        $this->assertEquals('id', $columns->get(0)->getName());
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnText', $columns->get(1));
        $this->assertEquals('name', $columns->get(1)->getName());
    }

    /**
     * @test
     */
    public function tableHasForiegnKeyColumn(){
        $columns = $this->sut->factory('riders');

        $this->assertEquals(3, $columns->count());

        $column = $columns->get(0);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnLabel', $column);
        $this->assertEquals('id', $column->getName());

        $column = $columns->get(1);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnSelect', $column);
        $this->assertEquals('class_id', $column->getName());
        $expected = [
            '1' => 'class1',
            '2' => 'class2',
        ];
        $this->assertEquals($expected, $column->getSelectList());

        $column = $columns->get(2);
        $this->assertInstanceOf('\Shin1x1\LaravelTableAdmin\Column\ColumnText', $column);
        $this->assertEquals('name', $column->getName());
    }
}